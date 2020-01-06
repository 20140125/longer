<?php

use Workerman\Lib\Timer;
use Workerman\Worker;
use PHPSocketIO\SocketIO;
use Workerman\MySQL\Connection;
require_once  '../../vendor/autoload.php';
require_once  __DIR__.'/config/db.php';
//日志今天的总量
$log_last_count = 0;
//通知今天的总量
$push_last_count = 0;
//授权用户总量
$oauth_last_count = 0;
//单个用户推送的总量
$push_data_count = 0;
//在线人数
$online_user_count = 0;
//时间跨度
$times = 15;
// PHPSocketIO服务  // window/苹果系统
if(in_array(PHP_OS,['WINNT','Darwin'])) {
    $sender_io = new SocketIO(2120);
} else {
    $context = array(
        'ssl' => array(
            'local_cert'  => '/www/server/panel/vhost/cert/www.fanglonger.com/fullchain.pem',//你证书的pem文件
            'local_pk'    => '/www/server/panel/vhost/cert/www.fanglonger.com/privkey.pem',//你证书的key文件
            'verify_peer' => false,
        )
    );
    $sender_io = new SocketIO(2120,$context);
}
// Redis 链接
$redis = new Redis();
$redis->connect('127.0.0.1',6379);
//数据库连接
$db = new Connection(Host, Port, UserName, Password, DbName);
$day = range(strtotime(date('Ymd',strtotime("-{$times} day"))),strtotime(date('Ymd')),24*60*60);
foreach ($day as &$item) {
    $item = date('Ymd',$item);
}
// 客户端发起连接事件时，设置连接socket的各种事件回调
$sender_io->on('connection', function($socket) {
    // 当客户端发来登录事件时触发
    $socket->on('login', function ($uid)use($socket) {
        global $redis,$online_user_count;
        $socket->uid = $uid;
        //判断值是否存在redis里面
        if ($redis->sIsMember('uidConnectionMap',$uid)) {
            return ;
        }
        $redis->sAdd('uidConnectionMap',$uid);
        $redisUser = $redis->SMEMBERS(RedisKey);
        $online_user_count = count($redisUser);
        // 将这个连接加入到uid分组，方便针对uid推送数据
        $socket->join($uid);
    });
    global $redis,$sender_io,$day,$log_last_count,$push_data_count,$push_last_count,$oauth_last_count;
    //已经登录的用户
    $redisUser = $redis->SMEMBERS(RedisKey);
    foreach ($redisUser as $user) {
        $pushData = pushData($user);
        $push_data_count = count($pushData);
        $sender_io->to($user)->emit('notice', $pushData);
    }
    //在线人数
    $sender_io->emit('online',count($redisUser));
    $total['day'] = $day;
    //每天的日志总量
    $logCount = getLogCount();
    $log_last_count = $logCount[intval(count($logCount)-1)];
    //每天的通知总量
    $pushCount = getPushCount();
    $push_last_count = $pushCount[intval(count($pushCount)-1)];
    //授权用户总量
    $oauthCount = getOauthCount();
    $oauth_last_count = $oauthCount[intval(count($oauthCount)-1)];
    //推送到图表数据
    $total['total'] = array('log' => $logCount,'push'=>$pushCount,'oauth'=>$oauthCount);
    $sender_io->emit('charts', $total);

    //用户离线
    $socket->on('disconnect', function () use($socket) {
        global $redis;
        if (!$redis->sIsMember(RedisKey,$socket->uid)) {
            return ;
        }
        //用户离线删除redis里的值
        $redis->sREM(RedisKey,$socket->uid);
    });
});

// 当$sender_io启动后监听一个http端口，通过这个端口可以给任意uid或者所有uid推送数据
$sender_io->on('workerStart', function () {
    // 监听一个http端口
    if(in_array(PHP_OS,['WINNT','Darwin'])) {
        $inner_http_worker = new Worker('http://0.0.0.0:2121');
    } else {
        $context = array(
            'ssl' => array(
                'local_cert' => '/www/server/panel/vhost/cert/www.fanglonger.com/fullchain.pem',//你证书的pem文件
                'local_pk' => '/www/server/panel/vhost/cert/www.fanglonger.com/privkey.pem',//你证书的key文件
                'verify_peer' => false,
            )
        );
        $inner_http_worker = new Worker('http://0.0.0.0:2121', $context);
        $inner_http_worker->transport = 'ssl';
    }
    // 当http客户端发来数据时触发
    $inner_http_worker->onMessage = function ($connection) {
        $_POST = $_POST ? $_POST : $_GET;
        switch (@$_POST['type']) {
            case 'publish':
                global $sender_io, $redis;
                $to = @$_POST['to'];
                $_POST['content'] = trim(htmlspecialchars(@$_POST['content']));
                // http接口返回，如果用户离线socket返回fail
                if ($to && !$redis->sIsMember(RedisKey, $to)) {
                    return $connection->send('offline');
                }
                if ($to) {
                    //向uid所在socket组发送数据
                    $sender_io->to($to)->emit('new_msg', $_POST['content']);
                } else {
                    // 向所有uid推送数据
                    $sender_io->emit('new_msg', @$_POST['content']);
                }
                return $connection->send('successfully');
        }
        return $connection->send('failed');
    };
    // 执行监听
    $inner_http_worker->listen();
    //定时器 (只有在客户端在线数变化了才广播，减少不必要的客户端通讯)
    Timer::add(2, function () {
        global $sender_io, $redis, $day,$log_last_count,$push_last_count,$online_user_count,$oauth_last_count,$times;
        $redisUser = $redis->SMEMBERS(RedisKey);
        foreach ($redisUser as $user) {
            $pushData = pushData($user);
            //站内通知推送
            $sender_io->to($user)->emit('notice', $pushData);
        }
        if ($day[count($day)-1] != date('Ymd')) {
            $day = range(strtotime(date('Ymd',strtotime("-{$times} day"))),strtotime(date('Ymd')),24*60*60);
            foreach ($day as &$item) {
                $item = date('Ymd',$item);
            }
        }
        //在线人数推送
        if ($online_user_count != count($redisUser)) {
            $sender_io->emit('online',count($redisUser));
        }
        $total['day'] = $day;
        $logCount = getLogCount();
        $pushCount = getPushCount();
        $oauthCount = getOauthCount();
        $total['total'] = array('log' => $logCount,'push'=>$pushCount,'oauth'=>$oauthCount);
        if ($log_last_count != $logCount[intval(count($logCount)-1)] ||  $push_last_count != $pushCount[intval(count($pushCount)-1)] || $oauth_last_count != $oauthCount[intval(count($oauthCount)-1)]) {
            //数据统计推送
            $sender_io->emit('charts', $total);
        }
    });
    /**
     * TODO:获取站内通知
     * @param $user
     * @return mixed
     */
    function pushData($user)
    {
        global $db;
        return $db->select('*')->from('os_push')->where("uid = '{$user}' ")->orderByDESC(['created_at'])->limit(10)->query();
    }
    /**
     * TODO:获取日志信息
     * @return array
     */
    function getLogCount()
    {
        global $db, $day,$times;
        $log = $db->select("day,count(*) as total")->from('os_system_log')
            ->where("day>=" . date('Ymd', strtotime("-{$times} day")))
            ->groupBy(['day'])->query();
        $logDay = $logTotal = array();
        foreach ($log as $value) {
            array_push($logDay, intval($value['day']));
        }
        foreach ($day as $item) {
            if (!in_array($item, $logDay)) {
                array_push($log, ['day' => $item, 'total' => 0]);
            }
        }
        array_multisort($log, SORT_ASC);
        foreach ($log as $item) {
            array_push($logTotal, intval($item['total']));
        }
        return $logTotal;
    }

    /**
     * TODO：获取站内通知
     * @return array
     */
    function getPushCount()
    {
        global $db, $day,$times;
        $push = $db->select("FROM_UNIXTIME(created_at,'%Y%m%d') as day,count(*) as total")->from('os_push')
            ->where("created_at>=" .strtotime(date('Y-m-d 23:59:59',strtotime("-{$times} day"))))
            ->groupBy(["created_at"])->query();
        $pushDay = $pushTotal = array();
        foreach ($push as $value) {
            array_push($pushDay, intval($value['day']));
        }
        foreach ($day as $item) {
            if (!in_array($item, $pushDay)) {
                array_push($push, ['day' => $item, 'total' => 0]);
            }
        }
        array_multisort($push, SORT_ASC);
        foreach ($push as $item) {
            array_push($pushTotal, intval($item['total']));
        }
        return $pushTotal;
    }
    /**
     * TODO：获取授权用户
     * @return array
     */
    function getOauthCount()
    {
        global $db, $day,$times;
        $oauth = $db->select("FROM_UNIXTIME(created_at,'%Y%m%d') as day,count(*) as total")->from('os_oauth')
            ->where("created_at>=" .strtotime(date('Y-m-d 23:59:59',strtotime("-{$times} day"))))
            ->groupBy(["created_at"])->query();
        $oauthDay = $oauthTotal = array();
        foreach ($oauth as $value) {
            array_push($oauthDay, intval($value['day']));
        }
        foreach ($day as $item) {
            if (!in_array($item, $oauthDay)) {
                array_push($oauth, ['day' => $item, 'total' => 0]);
            }
        }
        array_multisort($oauth, SORT_ASC);
        foreach ($oauth as $item) {
            array_push($oauthTotal, intval($item['total']));
        }
        return $oauthTotal;
    }
});
if(!defined('GLOBAL_START')) {
    Worker::runAll();
}

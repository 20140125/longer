<?php

use Workerman\Lib\Timer;
use Workerman\Worker;
use PHPSocketIO\SocketIO;
use Workerman\MySQL\Connection;
include  '../../vendor/autoload.php';
include __DIR__.'/config/db.php';
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
$db = new Connection(Host, Port, UserName, Password, DbName);
$day = range(strtotime(date('Ymd',strtotime('-7 day'))),strtotime(date('Ymd')),24*60*60);
foreach ($day as &$item) {
    $item = date('Ymd',$item);
}
// 客户端发起连接事件时，设置连接socket的各种事件回调
$sender_io->on('connection', function($socket) {
    // 当客户端发来登录事件时触发
    $socket->on('login', function ($uid)use($socket) {
        // 已经登录过了
        global $redis;
        $socket->uid = $uid;
        //判断值是否存在redis里面
        if ($redis->sIsMember('uidConnectionMap',$uid)) {
            return ;
        }
        $redis->sAdd('uidConnectionMap',$uid);
        // 将这个连接加入到uid分组，方便针对uid推送数据
        $socket->join($uid);
    });

    //用户离线
    $socket->on('disconnect', function () use($socket) {
        global $redis;
        if (!$redis->sIsMember('uidConnectionMap',$socket->uid)) {
            return ;
        }
        //用户离线删除redis里的值
        $redis->sREM('uidConnectionMap',$socket->uid);
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
                if ($to && !$redis->sIsMember('uidConnectionMap', $to)) {
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
    //定时器
    Timer::add(2, function () {
        global $sender_io, $db, $redis, $day;
        $redisUser = $redis->SMEMBERS('uidConnectionMap');
        foreach ($redisUser as $user) {
            $pushData = $db->select('*')->from('os_push')->where("see = 0 and state<> 'successfully' and uid = '{$user}' ")->query();
            $sender_io->emit('notice', $pushData);
        }
        $total['day'] = $day;
        $total['total'] = array('log' => getLogCount(),'push'=>getPushCount());
        $sender_io->emit('charts', $total);
    });
    /**
     * TODO:获取日志信息
     * @return array
     */
    function getLogCount()
    {
        global $db, $day;
        $log = $db->select("day,count(*) as total")->from('os_system_log')->where("day>=" . date('Ymd', strtotime('-7 day')))->groupBy(['day'])->query();
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
        global $db, $day;
        $push = $db->select("FROM_UNIXTIME(created_at,'%Y%m%d') as day,count(*) as total")->from('os_push')
            ->where("created_at>=" .strtotime(date('Y-m-d 00:00:00',strtotime('-7 day'))))
            ->groupBy(["from_unixtime(created_at,'%Y%m%d')"])->query();
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
});
if(!defined('GLOBAL_START')) {
    Worker::runAll();
}

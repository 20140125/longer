<?php
use Workerman\Worker;
use PHPSocketIO\SocketIO;
include __DIR__ . '/vendor/autoload.php';
// 记录最后一次广播的在线用户数
$last_online_count = 0;
// 记录最后一次广播的在线页面数
$last_online_page_count = 0;
// PHPSocketIO服务
if(strpos(strtolower(PHP_OS), 'win') === 0) {
    $sender_io = new SocketIO(2120);
} else {
    $context = array(
        'ssl' => array(
            'local_cert'  => '/www/server/panel/vhost/cert/www.fanglonger.com/fullchain.pem',//你证书的pem文件
            'local_pk'    => '/www/server/panel/vhost/cert/www.fanglonger.com/privkey.pem',//你证书的key文件
            'verify_peer' => false,
        )
    );
    $sender_io = new SocketIO(2120);
}
// Redis 链接
$redis = new Redis();
$redis->connect('127.0.0.1',6379);
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
$sender_io->on('workerStart', function() {
    // 监听一个http端口
    if(strpos(strtolower(PHP_OS), 'win') === 0) {
        $inner_http_worker = new Worker('http://0.0.0.0:2121');
    } else {
        $context = array(
            'ssl' => array(
                'local_cert'  => '/www/server/panel/vhost/cert/www.fanglonger.com/fullchain.pem',//你证书的pem文件
                'local_pk'    => '/www/server/panel/vhost/cert/www.fanglonger.com/privkey.pem',//你证书的key文件
                'verify_peer' => false,
            )
        );
        $inner_http_worker = new Worker('http://0.0.0.0:2121',$context);
        $inner_http_worker->transport = 'ssl';
    }
    // 当http客户端发来数据时触发
    $inner_http_worker->onMessage = function($connection) {
        $_POST = $_POST ? $_POST : $_GET;
        switch(@$_POST['type']){
            case 'publish':
                global $sender_io,$redis;
                $to = @$_POST['to'];
                $_POST['content'] = trim(htmlspecialchars(@$_POST['content']));
                // http接口返回，如果用户离线socket返回fail
                if($to && !$redis->sIsMember('uidConnectionMap',$to)) {
                    return $connection->send('offline');
                }
                if($to) {
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
});
if(!defined('GLOBAL_START')) {
    Worker::runAll();
}

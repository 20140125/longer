<?php
use Workerman\Worker;
use PHPSocketIO\SocketIO;
include __DIR__ . '/vendor/autoload.php';

// 全局数组保存uid在线数据
$uidConnectionMap = array();
// 记录最后一次广播的在线用户数
$last_online_count = 0;
// 记录最后一次广播的在线页面数
$last_online_page_count = 0;
// PHPSocketIO服务
$sender_io = new SocketIO(2120);
// 客户端发起连接事件时，设置连接socket的各种事件回调
$sender_io->on('connection', function($socket) {
    // 当客户端发来登录事件时触发
    $socket->on('login', function ($uid)use($socket) {
        global $uidConnectionMap, $last_online_count, $last_online_page_count;
        // 已经登录过了
        if(isset($socket->uid)) {
            return;
        }
        // 更新对应uid的在线数据
        $uid = (string)$uid;
        if(!isset($uidConnectionMap[$uid])) {
            $uidConnectionMap[$uid] = 0;
        }
        // 这个uid有++$uidConnectionMap[$uid]个socket连接
        ++$uidConnectionMap[$uid];
        // 将这个连接加入到uid分组，方便针对uid推送数据
        $socket->join($uid);
        $socket->uid = $uid;
    });
    //当客户端发送消息过来触发
    $socket->on('chat_web',function ($data)use($socket) {
        global $sender_io;
        switch ($data['type']) {
            case 'get_oauth':
                $sender_io->to('admin')->emit('chat_client',$data);
                break;
            case 'set_oauth':
                $sender_io->to($data['username'])->emit('chat_client',$data);
                break;
        }
    });
    // 当客户端断开连接是触发（一般是关闭网页或者跳转刷新导致）
    $socket->on('disconnect', function () use($socket) {
        if(!isset($socket->uid)) {
             return;
        }
        global $uidConnectionMap, $sender_io;
        // 将uid的在线socket数减一
        if(--$uidConnectionMap[$socket->uid] <= 0) {
            unset($uidConnectionMap[$socket->uid]);
        }
        //告知客户端用户离线
        $sender_io->to(md5('admin'))->emit('logout',$socket->uid.'已经离线');
    });
});

// 当$sender_io启动后监听一个http端口，通过这个端口可以给任意uid或者所有uid推送数据
$sender_io->on('workerStart', function() {
    // 监听一个http端口
    $inner_http_worker = new Worker('http://0.0.0.0:2121');
    // 当http客户端发来数据时触发
    $inner_http_worker->onMessage = function($http_connection, $data) {
        global $uidConnectionMap;
        $_POST = $_POST ? $_POST : $_GET;
        switch(@$_POST['type']){
            case 'publish':
                global $sender_io;
                $to = @$_POST['to'];
                $_POST['content'] = trim(htmlspecialchars(@$_POST['content']));
                // 有指定uid则向uid所在socket组发送数据
                if($to) {
                    $sender_io->to($to)->emit('new_msg', $_POST['content']);
                } else {
                    // 否则向所有uid推送数据
                    $sender_io->emit('new_msg', @$_POST['content']);
                }
                // http接口返回，如果用户离线socket返回fail
                if($to && !isset($uidConnectionMap[$to])) {
                    return $http_connection->send('offline');
                } else {
                    return $http_connection->send('ok');
                }
                break;
        }
        return $http_connection->send('fail');
    };
    // 执行监听
    $inner_http_worker->listen();
});
if(!defined('GLOBAL_START')) {
    Worker::runAll();
}

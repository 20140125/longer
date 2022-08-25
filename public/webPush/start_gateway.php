<?php
/**
 * This file is part of workerman.
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the MIT-LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @author walkor<walkor@workerman.net>
 * @copyright walkor<walkor@workerman.net>
 * @link http://www.workerman.net/
 * @license http://www.opensource.org/licenses/mit-license.php MIT License
 */

use \Workerman\Worker;
use \GatewayWorker\Gateway;

require_once '../../vendor/autoload.php';

// gateway 进程
if (in_array(PHP_OS, ['WINNT', 'Darwin'])) {
    $gateway = new Gateway('Websocket://0.0.0.0:7272');
} else {
    $context = array(
        'ssl' => array(
            'local_cert'  => '/www/server/panel/vhost/cert/www.fanglonger.com/fullchain.pem',//你证书的pem文件
            'local_pk'    => '/www/server/panel/vhost/cert/www.fanglonger.com/privkey.pem',//你证书的key文件
            'verify_peer' => false,
        )
    );
    $gateway = new Gateway('Websocket://0.0.0.0:7272', $context);
    // 开启SSL，websocket+SSL 即wss
    $gateway->transport = 'ssl';
}
// 设置名称，方便status时查看
$gateway->name = 'ChatGateway';
// 设置进程数，gateway进程数建议与cpu核数相同
$gateway->count = 1;
// 分布式部署时请设置成内网ip（非127.0.0.1）
$gateway->lanIp = '127.0.0.1';
// 内部通讯起始端口。假如$gateway->count=4，起始端口为2300
// 则一般会使用2300 2301 2302 2303 4个端口作为内部通讯端口
$gateway->startPort = 2300;
// 心跳间隔
$gateway->pingInterval = 1;
//如果pingNotResponseLimit = 1，则代表客户端必须定时发送心跳给服务端，否则pingNotResponseLimit*pingInterval=30秒内没有任何数据发来则关闭对应连接，并触发onClose。
$gateway->pingNotResponseLimit = 1;
// 心跳数据
$gateway->pingData = '{"type":"ping"}';
// 服务注册地址
$gateway->registerAddress = '127.0.0.1:1236';
// 如果不是在根目录启动，则运行runAll方法
if (!defined('GLOBAL_START')) {
    Worker::runAll();
}

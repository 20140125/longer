<?php

namespace App\Events\WorkerMan;

use GatewayWorker\Lib\Gateway;

class Events
{

    public static function onWorkerStart($businessWorker)
    {
        echo 'onWorkerStart';
    }

    /**
     * 当客户端连接时触发
     * 如果业务不需此回调可以删除onConnect
     * @param $client_id
     * @throws \Exception
     */
    public static function onConnect($client_id)
    {
        // 向当前client_id发送数据
        Gateway::sendToClient($client_id, "Hello $client_id");
        // 向所有人发送
        Gateway::sendToAll("$client_id login");
    }

    /**
     * 当客户端发来消息时触发
     * @param $client_id
     * @param $message
     * @throws \Exception
     */
    public static function onMessage($client_id, $message)
    {
        // 向所有人发送
        Gateway::sendToAll("$client_id said $message\r\n");
    }

    /**
     * 当用户断开连接时触发
     * @param $client_id
     * @throws \Exception
     */
    public static function onClose($client_id)
    {
        // 向所有人发送
        GateWay::sendToAll("$client_id logout\r\n");
    }
}

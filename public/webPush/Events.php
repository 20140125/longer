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
/**
 * 用于检测业务代码死循环或者长时间阻塞等问题
 * 如果发现业务卡死，可以将下面declare打开（去掉//注释），并执行php start.php reload
 * 然后观察一段时间workerman.log看是否有process_timeout异常
 */
//declare(ticks=1);

/**
 * 聊天主逻辑
 * 主要是处理 onMessage onClose
 */

use \GatewayWorker\Lib\Gateway;
use \Workerman\MySQL\Connection;

require_once __DIR__ . '/config/db.php';
date_default_timezone_set("Asia/Shanghai");

class Events
{
    protected static $db;
    protected static $chat;
    protected static $redisUsers;

    /**
     * 有消息时
     * @param $from_client_id //workerman 生成的client_id
     * @param $message
     * @return bool
     * @throws Exception|boolean
     */
    public static function onMessage($from_client_id, $message)
    {
        /* 客户端传递的是json数据 */
        $message_data = json_decode($message, true);
        if (!$message_data) {
            return false;
        }
        self::$chat = new Chat();
        /* 获取在线用户 */
        self::$redisUsers = self::$chat->sMembers('laravel_database_'.REDIS_KEY);
        $clients_list = self::getUserLists(self::$redisUsers);
        /* 根据类型执行不同的业务 */
        switch ($message_data['type']) {
            /* 客户端回应服务端的心跳 */
            case 'pong':
                echo '';
                break;
            /* 客户端登录 message格式: {type:login, name:xx, room_id:1} ，添加到客户端，广播给所有客户端xx进入聊天室 */
            case 'login':
                /* 判断是否有房间号 */
                if (!isset($message_data['room_id'])) {
                    throw new Exception("\$message_data['room_id'] not set. client_ip:{$_SERVER['REMOTE_ADDR']} \$message:$message");
                }
                /* 把房间号昵称放到session中 */
                $room_id = $message_data['room_id'];
                $from_client_name = htmlspecialchars($message_data['client_name']);
                $_SESSION['room_id'] = $room_id;
                $_SESSION['client_name'] = $from_client_name;
                $_SESSION['uuid'] = $message_data['uuid'];
                /* 添加用户到redis */
                if (!self::$chat->sIsMember('laravel_database_'.REDIS_KEY, $message_data['uuid'])) {
                    self::$chat->sAdd('laravel_database_'.REDIS_KEY, $message_data['uuid']);
                }
                /* 转播给当前房间的所有客户端，xx进入聊天室 message {type:login, client_id:xx, name:xx} */
                $new_message = array(
                    'type'             => 'login',
                    'from_client_id'   => $message_data['from_client_id'],
                    'from_client_name' => htmlspecialchars($from_client_name),
                    'to_client_id'     => 'all',
                    'to_client_name'   => 'all',
                    'time'             => date('Y-m-d H:i:s'),
                    'client_img'       => $message_data['client_img'],
                    'client_lists'     => $clients_list,
                    'room_id'          => $room_id,
                    'uuid'             => $message_data['uuid']
                );
                /* 加入群聊房间 */
                Gateway::joinGroup($from_client_id, $room_id);
                /* 推送消息到房间 */
                Gateway::sendToGroup($room_id, json_encode($new_message));
                /* 将client_id与uuid绑定，以便通过Gateway::sendToUid($uuid)发送数据，通过Gateway::isUidOnline($uuid)用户是否在线。 */
                Gateway::bindUid($from_client_id, $message_data['uuid']);
                /* 给当前用户发送登录用户信息 */
                Gateway::sendToCurrentClient(json_encode($new_message));
                break;
            /* 客户端发言 message: {type:say, to_client_id:xx, content:xx} */
            case 'say':
                $from_client_name = $_SESSION['client_name'];
                /* 私聊 */
                if ($message_data['to_client_id'] != 'all') {
                    $new_message = array(
                        'type'             => 'say',
                        'from_client_id'   => $message_data['from_client_id'], //发送人
                        'from_client_name' => $from_client_name,
                        'to_client_id'     => $message_data['to_client_id'], //接收人
                        'to_client_name'   => $message_data['to_client_name'],
                        'content'          => nl2br(htmlspecialchars($message_data['content'])),
                        'time'             => date('Y-m-d H:i:s'),
                        'client_img'       => $message_data['client_img'],
                        'uuid'             => $message_data['uuid'], //接收人的uuid
                        'room_id'          => ''  //私聊房间号置空。
                    );
                    $new_message['client_lists'] = $clients_list;
                    /* 通过uuid发送消息 */
                    Gateway::sendToUid($message_data['to_client_id'], json_encode($new_message));
                    break;
                }
                /* 非法请求 */
                if (!isset($_SESSION['room_id'])) {
                    throw new Exception("\$_SESSION['room_id'] not set. client_ip:{$_SERVER['REMOTE_ADDR']}");
                }
                /* 群聊 */
                $new_message = array(
                    'type'             => 'say',
                    'from_client_id'   => $message_data['from_client_id'],
                    'from_client_name' => $from_client_name,
                    'to_client_id'     => 'all',
                    'to_client_name'   => 'all',
                    'content'          => nl2br(htmlspecialchars($message_data['content'])),
                    'time'             => date('Y-m-d H:i:s'),
                    'client_img'       => $message_data['client_img'],
                    'uuid'             => $message_data['uuid'], //发送人的uuid
                    'room_id'          => $message_data['room_id']
                );
                $new_message['client_lists'] = $clients_list;
                /* 发送消息到当前组 */
                Gateway::sendToGroup($message_data['room_id'], json_encode($new_message));
                break;
            default:
                break;
        }
        return false;
    }

    /**
     * 当客户端断开连接时
     * @param $client_id
     * @throws Exception
     */
    public static function onClose($client_id)
    {
        self::$chat = new Chat();
        /* 从房间的客户端列表中删除 */
        if (isset($_SESSION['room_id'])) {
            $room_id = $_SESSION['room_id'];
            self::$chat->sRem('laravel_database_'.REDIS_KEY, $_SESSION['uuid']);
            $new_message = array(
                'type'             => 'logout',
                'from_client_id'   => $client_id,
                'from_client_name' => $_SESSION['client_name'],
                'time'             => date('Y-m-d H:i:s'),
                'uuid'             => $_SESSION['uuid'],
            );
            Gateway::sendToGroup($room_id, json_encode($new_message));
        }
    }
    /**
     * todo:获取管理员列表
     * @param $redisUser
     * @return array
     * @throws RedisException
     */
    protected static function getUserLists($redisUser): array
    {
        self::$chat = new Chat();
        $users = json_decode(self::$chat->sMembers('laravel_database_'.CHAT_KEY)[0], true);
        $arr = [];
        foreach ($users as $key => $item) {
            $users[$key]['type'] = 'login';
            $users[$key]['room_id'] = 1200;
            $users[$key]['online'] = false;
            foreach ($redisUser as $redis) {
                if ($users[$key]['uuid'] === $redis) {
                    $users[$key]['online'] = true;
                }
            }
            $arr[$item['uuid']] = $users[$key];
            $sort[$item['uuid']] = $item['online'];
        }
        array_multisort($sort, SORT_DESC, $arr);
        return $arr;
    }
}

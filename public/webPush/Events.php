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
require_once __DIR__.'/config/db.php';
date_default_timezone_set("Asia/Shanghai");
class Events
{
    /**
     * 有消息时
     * @param $client_id
     * @param $message
     * @return bool
     * @throws Exception
     */
    public static function onMessage($client_id, $message)
    {
        // 客户端传递的是json数据
        $message_data = json_decode($message, true);
        if(!$message_data) {
            return false;
        }
        $chat = new Chat();
        // 根据类型执行不同的业务
        switch($message_data['type']) {
            // 客户端回应服务端的心跳
            case 'pong':
                echo '';
                break;
            // 客户端登录 message格式: {type:login, name:xx, room_id:1} ，添加到客户端，广播给所有客户端xx进入聊天室
            case 'login':
                // 判断是否有房间号
                if(!isset($message_data['room_id'])) {
                    throw new \Exception("\$message_data['room_id'] not set. client_ip:{$_SERVER['REMOTE_ADDR']} \$message:$message");
                }
                // 把房间号昵称放到session中
                $room_id = $message_data['room_id'];
                $client_name = htmlspecialchars($message_data['client_name']);
                $_SESSION['room_id'] = $room_id;
                $_SESSION['client_name'] = $client_name;
                $_SESSION['client_img'] = $message_data['client_img'];
                // 获取房间内所有用户列表
                $clients_list = Gateway::getClientSessionsByGroup($room_id);
                foreach($clients_list as $tmp_client_id=>$item) {
                    $clients_list[$tmp_client_id] = $item;
                }
                $clients_list[$client_id] = $message_data;
                //群聊图像
                $arr = array(
                    'room_id' => $room_id,
                    'client_name' => 'all',
                    'client_img' => KFImg
                );
                array_push($clients_list,$arr);
                // 转播给当前房间的所有客户端，xx进入聊天室 message {type:login, client_id:xx, name:xx}
                $new_message = array(
                    'type'=>$message_data['type'],
                    'client_id'=>$client_id,
                    'client_name'=>htmlspecialchars($client_name),
                    'time'=>date('Y-m-d H:i:s'),
                    'client_img' => $message_data['client_img']
                );
                $new_message['client_list'] = $clients_list;
                Gateway::sendToGroup($room_id, json_encode($new_message));
                Gateway::joinGroup($client_id, $room_id);
                // 给当前用户发送用户列表
                Gateway::sendToCurrentClient(json_encode($new_message));
                break;
            // 获取聊天记录 message: {type:history, to_client_id:xx, content:xx}
            case 'history':
                $messageLists = $chat->getChatMsgLists($message_data['from_client_name'],$message_data['to_client_name']);
                $new_message = array(
                    'type'=>'history',
                    'from_client_name' => $message_data['from_client_name'],
                    'to_client_name' => $message_data['to_client_name'],
                    'message' => $messageLists
                );
                Gateway::sendToCurrentClient(json_encode($new_message));
                break;
            // 客户端发言 message: {type:say, to_client_id:xx, content:xx}
            case 'say':
                // 非法请求
                if(!isset($_SESSION['room_id'])) {
                    throw new \Exception("\$_SESSION['room_id'] not set. client_ip:{$_SERVER['REMOTE_ADDR']}");
                }
                $room_id = $_SESSION['room_id'];
                $client_name = $_SESSION['client_name'];
                // 私聊
                if($message_data['to_client_id'] != 'all') {
                    $new_message = array(
                        'type'=>'say',
                        'from_client_id'=>$client_id,
                        'from_client_name' =>$client_name,
                        'to_client_id'=>$message_data['to_client_id'],
                        'to_client_name'=>$message_data['to_client_name'],
                        'content'=>nl2br(htmlspecialchars($message_data['content'])),
                        'time'=>date('Y-m-d H:i:s'),
                        'msg_type' => $message_data['msg_type'],
                        'avatar_url' => $message_data['avatar_url']
                    );
                    //保存聊天记录
                    $chat->setChatMsgLists($client_name,$message_data['to_client_name'],$new_message);
                    //发送到客户端
                    Gateway::sendToClient($message_data['to_client_id'], json_encode($new_message));
                    //发送到当前客户端
                    Gateway::sendToCurrentClient(json_encode($new_message));
                    break;
                }
                //群聊
                $new_message = array(
                    'type'=>'say',
                    'from_client_id'=>$client_id,
                    'from_client_name' =>$client_name,
                    'to_client_id'=>'all',
                    'to_client_name' => 'all',
                    'content'=>nl2br(htmlspecialchars($message_data['content'])),
                    'time'=>date('Y-m-d H:i:s'),
                    'msg_type' => $message_data['msg_type'],
                    'avatar_url' => $message_data['avatar_url']
                );
                //保存聊天记录
                $chat->setChatMsgLists($client_name,'all',$new_message);
                Gateway::sendToGroup($room_id ,json_encode($new_message));
                break;
            default:
                break;
        }
        return false;
    }

    /**
     * 当客户端断开连接时
     * @param integer $client_id 客户端id
     * @throws Exception
     */
    public static function onClose($client_id)
    {
        // 从房间的客户端列表中删除
        if(isset($_SESSION['room_id'])) {
            $room_id = $_SESSION['room_id'];
            $new_message = array(
                'type'=>'logout',
                'from_client_id'=>$client_id,
                'from_client_name'=>$_SESSION['client_name'],
                'time'=>date('Y-m-d H:i:s')
            );
            Gateway::sendToGroup($room_id, json_encode($new_message));
        }
    }
}
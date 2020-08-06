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
declare(ticks=1);
/**
 * 聊天主逻辑
 * 主要是处理 onMessage onClose
 */
use \GatewayWorker\Lib\Gateway;
use \Workerman\MySQL\Connection;

require_once __DIR__.'/config/db.php';
date_default_timezone_set("Asia/Shanghai");
class Events
{
    static protected $db='';
    static protected $chat;
    static protected $redisUsers;
    /**
     * 有消息时
     * @param $from_client_id  //workerman 生成的client_id
     * @param $message
     * @return bool
     * @throws Exception
     */
    public static function onMessage($from_client_id, $message)
    {
        // 客户端传递的是json数据
        $message_data = json_decode($message, true);
        if(!$message_data) {
            return false;
        }
        self::$chat = new Chat();
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
                $from_client_name = htmlspecialchars($message_data['client_name']);
                $_SESSION['room_id'] = $room_id;
                $_SESSION['client_name'] = $from_client_name;
                $_SESSION['client_img'] = $message_data['client_img'];
                $_SESSION['uid'] = $message_data['uid'];
                //添加用户到redis
                if (!self::$chat->sIsMember(RedisKey,$message_data['uid'])) {
                    self::$chat->sAdd(RedisKey, $message_data['uid']);
                }
                // 获取在线用户
                self::$redisUsers = self::$chat->sMembers(RedisKey);
                $clients_list = self::getUserLists(self::$redisUsers);
                // 转播给当前房间的所有客户端，xx进入聊天室 message {type:login, client_id:xx, name:xx}
                $new_message = array(
                    'type'=>'login',
                    'from_client_id'=>$message_data['from_client_id'],
                    'from_client_name'=>htmlspecialchars($from_client_name),
                    'to_client_id' => 'all',
                    'to_client_name' => 'all',
                    'time'=>date('Y-m-d H:i:s'),
                    'client_img' => $message_data['client_img'],
                    'client_list' => $clients_list,
                    'room_id' =>$room_id,
                    'uid' => $message_data['uid'],
                    'weather' => self::getWeather($message_data['adcode'])
                );
                Gateway::sendToGroup($room_id, json_encode($new_message));
                //加入群聊房间
                Gateway::joinGroup($from_client_id, $room_id);
                //将client_id与uid绑定，以便通过Gateway::sendToUid($uid)发送数据，通过Gateway::isUidOnline($uid)用户是否在线。
                Gateway::bindUid($from_client_id,$message_data['uid']);
                // 给当前用户发送用户列表
                Gateway::sendToCurrentClient(json_encode($new_message));
                break;
            // 获取聊天记录 message: {type:history, to_client_id:xx, content:xx}
            case 'history':
                $room_id = $message_data['room_id'];
                $messageLists = self::$chat->getChatMsgLists($message_data['from_client_id'],$message_data['to_client_id'],$room_id);
                $new_message = array(
                    'type'=>'history',
                    'from_client_name' => $message_data['from_client_name'],
                    'from_client_id' => $message_data['from_client_id'],
                    'to_client_name' => $message_data['to_client_name'],
                    'to_client_id' => $message_data['to_client_id'],
                    'room_id' =>$room_id,
                    'message' => $messageLists,
                    'uid' => $message_data['uid']
                );
                //清空未读状态
                self::$chat->delUnreadMsg($message_data['to_client_id'], $message_data['from_client_id']);
                //发送消息到当前客户端
                Gateway::sendToCurrentClient(json_encode($new_message));
                break;
            // 客户端发言 message: {type:say, to_client_id:xx, content:xx}
            case 'say':
                $from_client_name = $_SESSION['client_name'];
                // 私聊
                if($message_data['to_client_id'] != 'all') {
                    $new_message = array(
                        'type'=>'say',
                        'from_client_id'=>$message_data['from_client_id'], //发送人
                        'from_client_name' =>$from_client_name,
                        'to_client_id'=>$message_data['to_client_id'], //接收人
                        'to_client_name'=>$message_data['to_client_name'],
                        'content'=>nl2br(htmlspecialchars($message_data['content'])),
                        'time'=>date('Y-m-d H:i:s'),
                        'msg_type' => $message_data['msg_type'],
                        'avatar_url' => $message_data['avatar_url'],
                        'uid' => $message_data['uid'], //接收人的uid
                        'room_id' => ''  //私聊房间号置空。
                    );
                    //设置未读消息数
                    if (!in_array($message_data['uid'],self::$redisUsers)) {
                        self::$chat->setUnreadMsgLists($message_data['from_client_id'],$message_data['to_client_id']);
                    }
                    //保存聊天记录
                    self::$chat->setChatMsgLists($message_data['from_client_id'],$message_data['to_client_id'],'',$new_message);
                    //通过uid发送消息
                    Gateway::sendToUid($message_data['from_client_id'],json_encode($new_message));
                    break;
                }
                // 非法请求
                if(!isset($_SESSION['room_id'])) {
                    throw new \Exception("\$_SESSION['room_id'] not set. client_ip:{$_SERVER['REMOTE_ADDR']}");
                }
                $room_id = $_SESSION['room_id'];
                //群聊
                $new_message = array(
                    'type'=>'say',
                    'from_client_id'=>$message_data['from_client_id'],
                    'from_client_name' =>$from_client_name,
                    'to_client_id'=>'all',
                    'to_client_name' => 'all',
                    'content'=>nl2br(htmlspecialchars($message_data['content'])),
                    'time'=>date('Y-m-d H:i:s'),
                    'msg_type' => $message_data['msg_type'],
                    'avatar_url' => $message_data['avatar_url'],
                    'uid' => $message_data['uid'], //发送人的uid
                    'room_id' =>$room_id
                );
                //保存聊天记录
                self::$chat->setChatMsgLists($message_data['from_client_id'],'all',$room_id,$new_message);
                //添加到当前组
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
        self::$chat = new Chat();
        // 从房间的客户端列表中删除
        if(isset($_SESSION['room_id'])) {
            $room_id = $_SESSION['room_id'];
            self::$chat->sRem(RedisKey,$_SESSION['uid']);
            $new_message = array(
                'type'=>'logout',
                'from_client_id'=>$client_id,
                'from_client_name'=>$_SESSION['client_name'],
                'time'=>date('Y-m-d H:i:s'),
                'uid' => $_SESSION['uid'],
            );
            Gateway::sendToGroup($room_id, json_encode($new_message));
        }
    }

    /**
     * todo:获取管理员列表
     * @param $redisUser
     * @return array
     */
    public static function getUserLists($redisUser)
    {
        self::$chat = new Chat();
        $arr = [];$sortArr = [];
        $users = json_decode(self::$chat->sMembers(chatKey)[0],true);
        foreach ($users as $key=> $item) {
            $users[$key]['online'] = false; //是否在线
            $users[$key]['unreadCount'] = 0; //所有的未读消息数
            $users[$key]['total'] = 0;
            foreach ($redisUser as $redis) {
                if ($redis == $users[$key]['uid']) {
                    $users[$key]['online'] = true;
                }
            }
            $unreadMsg = self::$chat->getUnreadMsgAllCount($users[$key]['uid']);
            //单条用户展示未读消息数
            $unreadArr = array();
            if (count($unreadMsg)>0) {
                foreach ($unreadMsg as $from => $total) {
                    $unreadArr[] = ['form' => $from, 'total' => $total];
                    $users[$key]['unreadCount']+= $total;
                }
            }
            //未读消息数
            $users[$key]['unread'] = count($unreadMsg)>0 ? $unreadArr : 0;
            $sortArr[$users[$key]['uid']] = $users[$key]['online'];
            $users[$key]['type'] = 'login';
            $users[$key]['room_id'] = '1200';
            $arr[$users[$key]['uid']] = $users[$key];
        }
        array_multisort($sortArr,SORT_DESC,$arr);
        return $arr;
    }
    /**
     * todo:获取当前城市天气
     * @param $adcode
     * @return bool|mixed|string
     */
    public static function getWeather($adcode)
    {
        try {
            self::$chat = new Chat();
            if (!self::$chat->getValue($adcode)) {
                self::$db = new connection(Host,Port,UserName,Password,DbName);
                $result = self::$db->from('os_china_area')->where("code='$adcode'")->select('info,forecast')->query();
                $redisValue = json_encode(
                    [
                        'info'=>$result[0]['info'] ? json_decode($result[0]['info'],true) : '',
                        'forecast'=>$result[0]['forecast'] ? json_decode($result[0]['forecast'],true) : ''
                    ],
                    JSON_UNESCAPED_UNICODE);
                self::$chat->setValue($adcode,$redisValue,['EX'=>3600]);
                return $redisValue;
            }
            return self::$chat->getValue($adcode);
        } catch (\Exception $exception){
            self::$db->closeConnection();
            echo $exception;
        }
        return false;
    }
}

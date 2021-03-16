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

require_once __DIR__.'/config/db.php';
date_default_timezone_set("Asia/Shanghai");

class Events
{
    protected static $db = '';
    protected static $chat;
    protected static $redisUsers;
    /**
     * @todo 自动回复消息默认配置
     * @var string[]
     */
    protected static $sysRobot = [
        'client_id' => 'longer7f00000108fc00000001',
        'client_name' => 'systemRobot',
        'client_img' => 'https://cdn.pixabay.com/photo/2016/12/13/21/20/alien-1905155_960_720.png',
        'content' => '欢迎您的到来！'
    ];
    /**
     * 有消息时
     * @param $from_client_id  //workerman 生成的client_id
     * @param $message
     * @return bool
     * @throws \Exception|boolean
     */
    public static function onMessage($from_client_id, $message)
    {
        // 客户端传递的是json数据
        $message_data = json_decode($message, true);
        if (!$message_data) {
            return false;
        }
        self::$chat = new Chat();
        // 获取在线用户
        self::$redisUsers = self::$chat->sMembers(REDIS_KEY);
        $clients_list = self::getUserLists(self::$redisUsers);
        // 根据类型执行不同的业务
        switch ($message_data['type']) {
            // 客户端回应服务端的心跳
            case 'pong':
                echo '';
                break;
            // 客户端登录 message格式: {type:login, name:xx, room_id:1} ，添加到客户端，广播给所有客户端xx进入聊天室
            case 'login':
                // 判断是否有房间号
                if (!isset($message_data['room_id'])) {
                    throw new \Exception("\$message_data['room_id'] not set. client_ip:{$_SERVER['REMOTE_ADDR']} \$message:$message");
                }
                // 把房间号昵称放到session中
                $room_id = $message_data['room_id'];
                $from_client_name = htmlspecialchars($message_data['client_name']);
                $_SESSION['room_id'] = $room_id;
                $_SESSION['client_name'] = $from_client_name;
                $_SESSION['uid'] = $message_data['uid'];
                //添加用户到redis
                if (!self::$chat->sIsMember(REDIS_KEY, $message_data['uid'])) {
                    self::$chat->sAdd(REDIS_KEY, $message_data['uid']);
                }
                self::$redisUsers = self::$chat->sMembers(REDIS_KEY);
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
                Gateway::bindUid($from_client_id, $message_data['uid']);
                // 给当前用户发送用户列表
                Gateway::sendToCurrentClient(json_encode($new_message));
                break;
            // 获取聊天记录 message: {type:history, to_client_id:xx, content:xx}
            case 'history':
                //清空未读状态
                if (!empty($message_data['source']) && $message_data['source'] === 'user') {
                    self::$chat->delUnreadMsg($message_data['from_client_id'], $message_data['to_client_id']);
                }
                $redisMessage = self::$chat->getChatMsgLists($message_data['from_client_id'], $message_data['to_client_id'], $message_data['room_id'], $message_data['page'], $message_data['limit']);
                //数据库查询历史记录
                $hisMessage = array(
                    'from_client_id' => $message_data['from_client_id'],
                    'to_client_id' => $message_data['to_client_id'],
                    'room_id' => $message_data['room_id'],
                    'page' => $message_data['page'],
                    'limit' => $message_data['limit'] + 1
                );
                if (ceil($redisMessage['total'] / $hisMessage['limit']) > $hisMessage['page']) {
                    $messageLists = $redisMessage;
                } else {
                    $messageLists = self::getHisChatMessage($redisMessage, $hisMessage);
                }
                $new_message = array(
                    'type'=>'history',
                    'from_client_name' => $message_data['from_client_name'],
                    'from_client_id' => $message_data['from_client_id'],
                    'to_client_name' => $message_data['to_client_name'],
                    'to_client_id' => $message_data['to_client_id'],
                    'room_id' =>$message_data['room_id'],
                    'message' => $messageLists['list'],
                    'total' => $messageLists['total'],
                    'uid' => $message_data['uid'],
                    'page' => $message_data['page'],
                    'limit' => $message_data['limit'],
                    'client_list' => $clients_list,
                    'source' => $message_data['source']
                );
                //发送消息到当前客户端
                Gateway::sendToCurrentClient(json_encode($new_message));
                break;
            // 客户端发言 message: {type:say, to_client_id:xx, content:xx}
            case 'say':
                $from_client_name = $_SESSION['client_name'];
                // 私聊
                if ($message_data['to_client_id'] != 'all') {
                    $new_message = array(
                        'type'=>'say',
                        'from_client_id'=>$message_data['from_client_id'], //发送人
                        'from_client_name' =>$from_client_name,
                        'to_client_id'=>$message_data['to_client_id'], //接收人
                        'to_client_name'=>$message_data['to_client_name'],
                        'content'=>nl2br(htmlspecialchars($message_data['content'])),
                        'time'=>date('Y-m-d H:i:s'),
                        'msg_type' => $message_data['msg_type'],
                        'client_img' => $message_data['client_img'],
                        'uid' => $message_data['uid'], //接收人的uid
                        'room_id' => ''  //私聊房间号置空。
                    );
                    //设置未读消息数
                    self::$chat->setUnreadMsgLists($message_data['from_client_id'], $message_data['to_client_id']);
                    //保存聊天记录
                    self::$chat->setChatMsgLists(
                        $message_data['from_client_id'],
                        $message_data['to_client_id'],
                        '',
                        $new_message
                    );
                    $new_message['client_list'] = $clients_list;
                    //通过uid发送消息
                    Gateway::sendToUid($message_data['to_client_id'], json_encode($new_message));
                    break;
                }
                // 非法请求
                if (!isset($_SESSION['room_id'])) {
                    throw new \Exception("\$_SESSION['room_id'] not set. client_ip:{$_SERVER['REMOTE_ADDR']}");
                }
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
                    'client_img' => $message_data['client_img'],
                    'uid' => $message_data['uid'], //发送人的uid
                    'room_id' =>$message_data['room_id']
                );
                //保存聊天记录
                self::$chat->setChatMsgLists(
                    $message_data['from_client_id'],
                    'all',
                    $message_data['room_id'],
                    $new_message
                );
                $new_message['client_list'] = $clients_list;
                //发送消息到当前组
                Gateway::sendToGroup($message_data['room_id'], json_encode($new_message));
                break;
            //删除记录
            case 'srem':
                self::$chat->recallMessage($message_data);
                self::recallOrSRemMessage($message_data, $clients_list);
                break;
            //消息撤回
            case 'recall':
                self::$chat->recallMessage($message_data['recall_message']);
                //保存聊天记录
                unset($message_data['recall_message']);
                self::$chat->setChatMsgLists(
                    $message_data['from_client_id'],
                    $message_data['to_client_id'],
                    $message_data['room_id'],
                    $message_data
                );
                self::recallOrSRemMessage($message_data, $clients_list);
                break;
            default:
                break;
        }
        return false;
    }
    /**
     * todo:消息撤回删除
     * @param $message_data
     * @param $clients_list
     */
    protected static function recallOrSRemMessage($message_data, $clients_list)
    {
        $message_data['client_list'] = $clients_list;
        $message_data['message'] = self::$chat->getChatMsgLists(
            $message_data['from_client_id'],
            $message_data['to_client_id'],
            $message_data['room_id']
        ) ?? [];
        if (!empty($message_data['room_id'])) {
            //发送消息到当前组
            Gateway::sendToGroup($message_data['room_id'], json_encode($message_data));
        } else {
            //通过uid发送消息
            Gateway::sendToUid($message_data['to_client_id'], json_encode($message_data));
        }
    }
    /**
     * 当客户端断开连接时
     * @param $client_id //客户端id
     * @throws \Exception
     */
    public static function onClose($client_id)
    {
        self::$chat = new Chat();
        // 从房间的客户端列表中删除
        if (isset($_SESSION['room_id'])) {
            $room_id = $_SESSION['room_id'];
            self::$chat->sRem(REDIS_KEY, $_SESSION['uid']);
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
     * todo:自动回复消息
     * @param $room_id
     */
    protected static function getRobotMessage($room_id)
    {
        $new_message = array(
            'type'=>'say',
            'from_client_id'=> self::$sysRobot['client_id'],
            'from_client_name' => self::$sysRobot['client_name'],
            'to_client_id'=>'all',
            'to_client_name' => 'all',
            'content'=> $_SESSION['client_name'].self::$sysRobot['content'],
            'time'=>date('Y-m-d H:i:s', time()+30),
            'msg_type' => 'text',
            'client_img' => self::$sysRobot['client_img'],
            'uid' => self::$sysRobot['client_id'], //发送人的uid
            'room_id' =>$room_id
        );
        //添加到当前组
        Gateway::sendToGroup($room_id, json_encode($new_message));
    }
    /**
     * todo:获取管理员列表
     * @param $redisUser
     * @return array
     */
    protected static function getUserLists($redisUser)
    {
        self::$chat = new Chat();
        $arr = [];
        $sortArr = [];
        $users = json_decode(self::$chat->sMembers(CHAT_KEY)[0], true);
        foreach ($users as $key => $item) {
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
        array_multisort($sortArr, SORT_DESC);
        return $arr;
    }
    /**
     * todo:获取当前城市天气
     * @param $adCode
     * @return bool|mixed|string
     */
    protected static function getWeather($adCode)
    {
        try {
            self::$chat = new Chat();
            if (!self::$chat->getValue($adCode)) {
                self::$db = new connection(HOST, PORT, USERNAME, PASSWORD, DBNAME);
                $result = self::$db->from('os_china_area')->where("code='$adCode'")->select('info,forecast')->query();
                $redisValue = json_encode(
                    [
                        'info'=>$result[0]['info'] ? json_decode($result[0]['info'], true) : '',
                        'forecast'=>$result[0]['forecast'] ? json_decode($result[0]['forecast'], true) : ''
                    ],
                    JSON_UNESCAPED_UNICODE
                );
                self::$chat->setValue($adCode, $redisValue, ['EX'=>3600]);
                return $redisValue;
            }
            return self::$chat->getValue($adCode);
        } catch (\Exception $exception) {
            self::$db->closeConnection();
            echo $exception;
        }
        return false;
    }
    /**
     * todo:获取历史记录(数据库查询数据)
     * @param $redisMessage
     * @param $message
     * @return false|mixed|string|null
     */
    protected static function getHisChatMessage($redisMessage, $message)
    {
        try {
            self::$db = new connection(HOST, PORT, USERNAME, PASSWORD, DBNAME);
            $offset = $message['limit'] * ($message['page'] - 1);
            if (!empty($message['room_id'])) {
                //列表
                $lists = self::$db->from('os_chat')->orderByDESC(['id'])->where("room_id = '{$message['room_id']}'")
                    ->limit($message['limit'])
                    ->offset($offset)->select('content')->query();
                foreach ($lists as $item) {
                    array_push($redisMessage['list'], json_decode($item['content']));
                }
                $result['list'] = $redisMessage['list'];
                //总记录数
                $total = self::$db->from('os_chat')->where("room_id = '{$message['room_id']}'")
                    ->select('count(*) as total')->query();
                $result['total'] = (int)$total[0]['total'] + (int)$redisMessage['total'];
            } else {
                //列表
                $lists = self::$db->from('os_chat')
                    ->where("(from_client_id = '{$message['from_client_id']}'
                    and to_client_id = '{$message['to_client_id']}')
                    or from_client_id = '{$message['to_client_id']}' and to_client_id = '{$message['from_client_id']}'")
                    ->limit($message['limit'])->orderByDESC(['id'])->offset($offset)->select('content')->query();
                foreach ($lists as $item) {
                    array_push($redisMessage['list'], json_decode($item['content']));
                }
                $result['list'] = $redisMessage['list'];
                //总记录数
                $total = self::$db->from('os_chat')
                    ->where("(from_client_id = '{$message['from_client_id']}'
                    and to_client_id = '{$message['to_client_id']}')
                    or from_client_id = '{$message['to_client_id']}' and to_client_id = '{$message['from_client_id']}'")
                    ->select('count(*) as total')->query();
                $result['total'] = (int)$total[0]['total'] + (int)$redisMessage['total'];
            }
            return $result;
        } catch (\Exception $exception) {
            self::$db->closeConnection();
            echo $exception;
        }
        return false;
    }
}

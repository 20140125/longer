<?php
/**
 * @author <fl140125@gmail.com>
 * Class Chat
 */
class Chat
{
    /**
     * @var Redis $redisClient
     */
    protected $redisClient;
    /**
     * @var string $hashKey
     */
    protected $hashKey = 'unread_';
    /**
     * Chat constructor.
     */
    public function __construct()
    {
        $this->redisClient = new Redis();
        $this->redisClient->connect('127.0.0.1',6379);
    }
    /**
     * TODO：缓存用户聊天记录
     * @param $from
     * @param $to
     * @param $room_id
     * @param $message
     * @return bool|int
     */
    public function setChatMsgLists($from,$to,$room_id,$message)
    {
        $value = str_replace('\\','',json_encode($message,JSON_UNESCAPED_UNICODE));
        $keyName = $to == 'all' ? 'receive_all_'.$room_id : "receive_{$from}_{$to}";
        return $this ->redisClient-> lPush($keyName, $value);
    }
    /**
     * TODO：获取聊天记录
     * @param $from
     * @param $to
     * @param $room_id
     * @return array
     */
    public function getChatMsgLists($from, $to,$room_id)
    {
        $messageLists = array();
        //群聊消息
        if ($to == 'all' && !empty($room_id)) {
            $recName = 'receive_all_'.$room_id;
            $num = $this->getMsgLen($recName);
            $recList = $this ->redisClient-> lRange($recName, 0, (int)($num));
            $time = array();
            foreach ($recList as $item) {
                array_push($messageLists,json_decode($item,true));
                $time[] = $item['time'];
            }
            // foreach ($messageLists as $item) {
            // }
            array_multisort($time,SORT_ASC,$messageLists);
        } else if (empty($room_id) && $to!='all') { //私聊信息
            //接受的消息
            $recName =  "receive_{$from}_{$to}";
            $num = $this->getMsgLen($recName);
            $recList = $this ->redisClient-> lRange($recName, 0, (int)($num));
            //发送的消息
            $sendName = "receive_{$to}_{$from}";
            $num = $this->getMsgLen($sendName);
            $sendLists = $this ->redisClient-> lRange($sendName, 0, (int)($num));
            $messageLists = array();
            $time = array();
            foreach ($sendLists as $item) {
                array_push($messageLists,json_decode($item,true));
                $time[] = $item['time'];

            }
            foreach ($recList as $item) {
                array_push($messageLists,json_decode($item,true));
                $time[] = $item['time'];

            }
            // foreach ($messageLists as $item) {
            //     $time[] = $item['time'];
            // }
            array_multisort($time,SORT_ASC,$messageLists);
        }
        return $messageLists;
    }
    /**
     * TODO:获取聊天记录长度
     * @param $key
     * @return int
     */
    protected function getMsgLen($key)
    {
        return $this->redisClient->lLen($key);
    }
    /**
     * TODO：获取用户未读消息记录数(所有)
     * @param $to
     * @return array
     */
    public function getUnreadMsgAllCount($to) {
        return $this ->redisClient-> hGetAll($this->hashKey.$to);
    }

    /**
     * TODO：获取用户未读消息记录数（单个）
     * @param $to
     * @param $from
     * @return string
     */
    public function getUnreadMsgCount($to,$from) {
        return $this ->redisClient-> hGet($this->hashKey.$to,$from);
    }
    /**
     * TODO：包括所有未读消息内容的数组
     * @param $from
     * @param $to
     * @return array
     */
    public function getUnreadMsg($from, $to)
    {
        $countArr = $this -> getUnreadMsgAllCount($to);
        $count = $countArr[$from];
        $keyName = "receive_{$from}_{$to}";
        return $this ->redisClient -> lRange($keyName, 0, (int)($count));
    }
    /**
     * TODO:添加用户未读消息记录数
     * @param $from
     * @param $to
     * @return int
     */
    public function setUnreadMsgLists($from,$to)
    {
        return $this->redisClient->hIncrBy($this->hashKey.$to,$from,1);
    }
    /**
     * TODO:删除未读消息
     * @param $from
     * @param $to
     * @return bool|int
     */
    public function delUnreadMsg($from,$to)
    {
        return $this-> redisClient -> hDel($this->hashKey.$to, $from);
    }
    /**
     * TODO:判断是否存在
     * @param $key
     * @param $value
     * @return bool
     */
    public function sIsMember($key,$value)
    {
        return $this->redisClient->sIsMember($key,$value);
    }
    /**
     * TODO：获取集合
     * @param $key
     * @return array
     */
    public function sMembers($key)
    {
        return $this->redisClient->SMEMBERS($key);
    }

    /**
     * TODO:添加数据到集合
     * @param string $string
     * @param $uid
     * @return bool|int
     */
    public function sAdd(string $string, $uid)
    {
        return $this->redisClient->sAdd($string,$uid);
    }

    /**
     * todo:删除集合数据
     * @param string $string
     * @param $uid
     * @return int
     */
    public function sRem(string $string,$uid)
    {
        return $this->redisClient->sRem($string,$uid);
    }
}

<?php
class Chat
{
    protected $redisClient;
    protected $checkUserReadable = false;
    public function __construct()
    {
        $this->redisClient = new Redis();
        $this->redisClient->connect('127.0.0.1',6379);
    }

    /**
     * TODO：缓存用户聊天记录
     * @param $from
     * @param $to
     * @param $message
     * @return bool|int
     */
    public function setChatRecode($from,$to,$message)
    {
        $value = str_replace('\\','',json_encode($message,JSON_UNESCAPED_UNICODE));
        $keyName = "receive_{$from}_{$to}";
        $res = $this ->redisClient-> lPush($keyName, $value);
        //消息接受者无法立刻查看时，将消息设置为未读
        if (!$this -> checkUserReadable) {
            $this -> cacheUnreadMsg($from, $to);
        }
        return $res;
    }

    /**
     * TODO：获取聊天记录
     * @param $from
     * @param $to
     * @return array
     */
    public function getChatRecord($from, $to)
    {
        //接受的消息
        $recName = "receive_{$from}_{$to}";
        $num = $this->getRecordLen($recName);
        $recList = $this ->redisClient-> lRange($recName, 0, (int)($num));
        //发送的消息
        $sendName = "receive_{$to}_{$from}";
        $sendLists = $this ->redisClient-> lRange($sendName, 0, (int)($num));
        $messageLists = array();
        foreach ($sendLists as $item) {
            array_push($messageLists,json_decode($item,true));
        }
        foreach ($recList as $item) {
            array_push($messageLists,json_decode($item,true));
        }
        $time = array();
        foreach ($messageLists as $item) {
            $time[] = $item['time'];
        }
        array_multisort($time,SORT_ASC,$messageLists);
        return $messageLists;
    }

    /**
     * TODO:获取聊天记录长度
     * @param $key
     * @return int
     */
    protected function getRecordLen($key)
    {
        return $this->redisClient->lLen($key);
    }

    /**
     * TODO：获取用户未读消息记录数
     * @param $user
     * @return array
     */
    public function getUnreadMsgCount($user) {
        return $this ->redisClient-> hGetAll('unread_' . $user);
    }

    /**
     * TODO：包括所有未读消息内容的数组
     * @param $from
     * @param $to
     * @return array
     */
    public function getUnreadMsg($from, $to)
    {
        $countArr = $this -> getUnreadMsgCount($to);
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
    public function cacheUnreadMsg($from, $to)
    {
        return $this->redisClient->hIncrBy($from,$to,1);
    }

    /**
     * TODO:删除未读消息
     * @param $from
     * @param $to
     * @return bool|int
     */
    public function setUnreadToRead($from, $to) {
        return $this-> redisClient -> hDel('unread_' . $to, $from);
    }
}

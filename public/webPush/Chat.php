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
        $this->redisClient->connect('127.0.0.1', 6379);
    }

    /**
     * 缓存用户聊天记录
     * @param $from
     * @param $to
     * @param $room_id
     * @param $message
     * @return bool|int
     * @throws RedisException
     */
    public function setChatMsgLists($from, $to, $room_id, $message)
    {
        $value = str_replace('\\', '', json_encode($message, JSON_UNESCAPED_UNICODE));
        $keyName = $to == 'all' ? 'receive_all_' . $room_id : "receive_{$from}_$to";
        return $this->redisClient->lPush($keyName, $value);
    }

    /**
     * 获取聊天记录
     * @param $from
     * @param $to
     * @param $room_id
     * @param int $page
     * @param int $limit
     * @return array
     * @throws RedisException
     */
    public function getChatMsgLists($from, $to, $room_id, int $page = 1, int $limit = 19)
    {
        $message = array();
        $time = array();
        $offset = $limit * ($page - 1);
        //群聊消息
        if (!empty($room_id)) {
            $recName = 'receive_all_' . $room_id;
            $num = $this->getMsgLen($recName);
            $recList = $offset + $limit >= $num ? $this->redisClient->lRange($recName, $offset, $num) : $this->redisClient->lRange($recName, $offset, $offset + $limit);
            foreach ($recList as $item) {
                $message[] = json_decode($item, true);
                $time[] = json_decode($item, true)['time'];
            }
            array_multisort($message, $time, SORT_ASC);
            return array('lists' => $message, 'total' => $num);
        }
        //接受的消息
        $recName = "receive_{$from}_$to";
        $recNum = $this->getMsgLen($recName);
        $recList = $offset + $limit >= $recNum ? $this->redisClient->lRange($recName, $offset, $recNum) : $this->redisClient->lRange($recName, $offset, $offset + $limit);
        foreach ($recList as $item) {
            $message[] = json_decode($item, true);
            $time[] = json_decode($item, true)['time'];
        }
        //发送的消息
        $sendName = "receive_{$to}_$from";
        $sendNum = $this->getMsgLen($sendName);
        $sendLists = $offset + $limit >= $sendNum ? $this->redisClient->lRange($sendName, $offset, $sendNum) : $this->redisClient->lRange($sendName, $offset, $offset + $limit);
        foreach ($sendLists as $item) {
            $message[] = json_decode($item, true);
            $time[] = json_decode($item, true)['time'];
        }
        array_multisort($message, $time, SORT_ASC);
        return array('lists' => $message, 'total' => $recNum + $sendNum);
    }

    /**
     * 消息撤回：删除redis缓存数据并告知用户/消息删除：直接删除消息
     * @param $message
     * @throws RedisException
     */
    public function recallMessage($message)
    {
        $receiveKey = empty($message['room_id']) ? "receive_{$message['from_client_id']}_{$message['to_client_id']}" : "receive_all_{$message['room_id']}";
        $num = $this->getMsgLen($receiveKey);
        $recList = $this->redisClient->lRange($receiveKey, 0, $num);
        $newRecList = array();
        foreach ($recList as $index => $item) {
            if (!$this->compareJson(json_encode($message, JSON_UNESCAPED_UNICODE), $item)) {
                array_push($newRecList, json_decode($item, true));
            }
        }
        //删除redisKey重新赋值
        $this->redisClient->del($receiveKey);
        foreach ($newRecList as $row) {
            $this->setChatMsgLists($row['from_client_id'], $row['to_client_id'], $row['room_id'], $row);
        }
    }

    /**
     * json字符串比较
     * @param $jsonA
     * @param $jsonB
     * @param array $field
     * @return bool
     */
    protected function compareJson($jsonA, $jsonB, array $field = ['type'])
    {
        $jsonA = json_decode($jsonA, true);
        $jsonB = json_decode($jsonB, true);
        if (count($jsonB) !== count($jsonA)) {
            return false;
        }
        $total = count($jsonA);
        $num = count($field);
        foreach ($jsonA as $i => $a) {
            if (!in_array($i, $field) && $jsonA[$i] == $jsonB[$i]) {
                $num++;
            }
        }
        return $num === $total;
    }

    /**
     * 获取聊天记录长度
     * @param $key
     * @return int
     * @throws RedisException
     */
    protected function getMsgLen($key)
    {
        return $this->redisClient->lLen($key);
    }

    /**
     * 获取用户未读消息记录数(所有)
     * @param $to
     * @return array
     * @throws RedisException
     */
    public function getUnreadMsgAllCount($to)
    {
        return $this->redisClient->hGetAll($this->hashKey . $to);
    }

    /**
     * 获取用户未读消息记录数（单个）
     * @param $to
     * @param $from
     * @return string
     * @throws RedisException
     */
    public function getUnreadMsgCount($to, $from)
    {
        return $this->redisClient->hGet($this->hashKey . $to, $from);
    }

    /**
     * 包括所有未读消息内容的数组
     * @param $from
     * @param $to
     * @return array
     * @throws RedisException
     */
    public function getUnreadMsg($from, $to)
    {
        $countArr = $this->getUnreadMsgAllCount($to);
        $count = $countArr[$from];
        $keyName = "receive_{$from}_$to";
        return $this->redisClient->lRange($keyName, 0, (int)($count));
    }

    /**
     * 添加用户未读消息记录数
     * @param $from
     * @param $to
     * @return int
     * @throws RedisException
     */
    public function setUnreadMsgLists($from, $to)
    {
        return $this->redisClient->hIncrBy($this->hashKey . $to, $from, 1);
    }

    /**
     * 删除未读消息
     * @param $from
     * @param $to
     * @return bool|int
     * @throws RedisException
     */
    public function delUnreadMsg($from, $to)
    {
        return $this->redisClient->hDel($this->hashKey . $from, $to);
    }

    /**
     * 判断是否存在
     * @param $key
     * @param $value
     * @return bool
     * @throws RedisException
     */
    public function sIsMember($key, $value)
    {
        return $this->redisClient->sIsMember($key, $value);
    }

    /**
     * 获取集合
     * @param $key
     * @return array
     * @throws RedisException
     */
    public function sMembers($key)
    {
        return $this->redisClient->SMEMBERS($key);
    }

    /**
     * 添加数据到集合
     * @param string $string
     * @param $uid
     * @return bool|int
     */
    public function sAdd(string $string, $uid)
    {
        return $this->redisClient->sAdd($string, $uid);
    }

    /**
     * 数据存储 （Redis 字符串(String)）
     * @param $key
     * @param $value
     * @param int $timeout
     * @return bool
     * @throws RedisException
     */
    public function setValue($key, $value, int $timeout = 0)
    {
        return $this->redisClient->set($key, $value, $timeout);
    }

    /**
     * 数据获取（Redis 字符串(String)）
     * @param $key
     * @return bool|string
     * @throws RedisException
     */
    public function getValue($key)
    {
        return $this->redisClient->get($key);
    }

    /**
     * 删除集合数据
     * @param string $string
     * @param $uid
     * @return int
     */
    public function sRem(string $string, $uid)
    {
        return $this->redisClient->sRem($string, $uid);
    }
}

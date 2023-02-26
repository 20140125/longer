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
    protected Redis $redisClient;
    /**
     * @var string $hashKey
     */
    protected string $hashKey = 'unread_';

    /**
     * Chat constructor.
     */
    public function __construct()
    {
        $this->redisClient = new Redis();
        $this->redisClient->connect('127.0.0.1', 6379);
    }
    /**
     * TODO:获取聊天记录长度
     * @param $key
     * @return int
     * @throws RedisException
     */
    protected function lLen($key): int
    {
        return $this->redisClient->lLen($key);
    }

    /**
     * TODO：获取用户未读消息记录数(所有)
     * @param $to
     * @return array
     * @throws RedisException
     */
    public function hGetAll($to): array
    {
        return $this->redisClient->hGetAll($this->hashKey . $to);
    }

    /**
     * TODO：获取用户未读消息记录数（单个）
     * @param $to
     * @param $from
     * @return false|Redis|string
     * @throws RedisException
     */
    public function hGet($to, $from)
    {
        return $this->redisClient->hGet($this->hashKey . $to, $from);
    }

    /**
     * TODO:添加用户未读消息记录数
     * @param $from
     * @param $to
     * @return int
     * @throws RedisException
     */
    public function hIncrBy($from, $to): int
    {
        return $this->redisClient->hIncrBy($this->hashKey . $to, $from, 1);
    }

    /**
     * TODO:删除未读消息
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
     * TODO:判断是否存在
     * @param $key
     * @param $value
     * @return bool
     * @throws RedisException
     */
    public function sIsMember($key, $value): bool
    {
        return $this->redisClient->sIsMember($key, $value);
    }

    /**
     * TODO：获取集合
     * @param $key
     * @return array
     * @throws RedisException
     */
    public function sMembers($key): array
    {
        return $this->redisClient->SMEMBERS($key);
    }

    /**
     * TODO:添加数据到集合
     * @param string $string
     * @param $uid
     * @return bool|int
     * @throws RedisException
     */
    public function sAdd(string $string, $uid)
    {
        return $this->redisClient->sAdd($string, $uid);
    }

    /**
     * TODO：数据存储 （Redis 字符串(String)）
     * @param $key
     * @param $value
     * @param int $timeout
     * @return bool
     * @throws RedisException
     */
    public function setValue($key, $value, int $timeout = 0): bool
    {
        return $this->redisClient->set($key, $value, $timeout);
    }

    /**
     * TODO:数据获取（Redis 字符串(String)）
     * @param $key
     * @return bool|string
     * @throws RedisException
     */
    public function getValue($key)
    {
        return $this->redisClient->get($key);
    }

    /**
     * todo:删除集合数据
     * @param string $string
     * @param $uid
     * @return int
     * @throws RedisException
     */
    public function sRem(string $string, $uid): int
    {
        return $this->redisClient->sRem($string, $uid);
    }
}

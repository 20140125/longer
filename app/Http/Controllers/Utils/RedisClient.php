<?php

namespace App\Http\Controllers\Utils;

/**
 * Class RedisClient
 * @author <fl140125@gmail.com>
 * @package App\Http\Controllers\Utils
 */
class RedisClient
{
    /**
     * @var string $host
     */
    protected $host = '127.0.0.1';
    /**
     * @var int $port
     */
    protected $port = '6379';
    /**
     * @var string $password
     */
    protected $password = '';
    /**
     * @var \Redis $redisClient
     */
    protected $redisClient;
    /**
     * RedisClient constructor.
     * @param $host
     * @param int $port
     * @param string $password
     */
    public function __construct($host,$port='6379',$password='')
    {
        $this->redisClient = new \Redis();
        $this->host = $host;
        $this->port = $port;
        $this->password = $password;
        $this->redisClient->connect($this->host,$this->port);
        if (!empty($this->password)) {
            try{
                $this->redisClient->auth($this->password);
            }catch (\Exception $exception){
                echo $exception->getMessage();
            }
        }
        return $this->redisClient;
    }

    /**
     * TODO：数据存储 （Redis 字符串(String)）
     * @param $key
     * @param $value
     * @param int $timeout
     * @return bool
     */
    public function setValue($key,$value,$timeout = 0)
    {
        return $this->redisClient->set($key,$value,$timeout);
    }

    /**
     * TODO:数据获取（Redis 字符串(String)）
     * @param $key
     * @return bool|string
     */
    public function getValue($key)
    {
        return $this->redisClient->get($key);
    }

    /**
     * TODO:数据存储（Redis 集合(Set)）
     * @param $key
     * @param $value
     * @return int
     */
    public function sAdd($key,$value)
    {
        return $this->redisClient->sAdd($key,$value);
    }

    /**
     * TODO:数据获取（Redis 集合(Set)）
     * @param $key
     * @return array
     */
    public function sMembers($key)
    {
        return $this->redisClient->sMembers($key);
    }

    /**
     * TODO：判读数据是否存在（Redis 集合(Set)）
     * @param $key
     * @param $value
     * @return bool
     */
    public function sIsMember($key,$value)
    {
        return $this->redisClient->sIsMember($key,$value);
    }

    /**
     * TODO：数据删除（Redis 集合(Set)）
     * @param $key
     * @param $value
     * @return int
     */
    public function sRem($key,$value)
    {
        return $this->redisClient->sRem($key,$value);
    }

    /**
     * TODO:数据添加（列表头部 Redis 列表(List)）
     * @param $key
     * @param $value
     * @return bool|int
     */
    public function lPush($key,$value)
    {
        return $this->redisClient->lPush($key,$value);
    }
    /**
     * TODO:移除并返回列表的第一个元素（ Redis 列表(List)）
     * @param $key
     * @return bool|int
     */
    public function lPop($key)
    {
        return $this->redisClient->lPop($key);
    }
    /**
     * TODO:数据添加（列表尾部 Redis 列表(List)）
     * @param $key
     * @param $value
     * @return bool|int
     */
    public function rPush($key,$value)
    {
        return $this->redisClient->rPush($key,$value);
    }
    /**
     * TODO:移除并返回列表的最末元素（ Redis 列表(List)）
     * @param $key
     * @return bool|int
     */
    public function rPop($key)
    {
        return $this->redisClient->rPop($key);
    }
}

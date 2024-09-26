<?php

namespace App\Http\Controllers\Utils;

use App\Http\Controllers\Controller;
use RedisException;

/**
 * Class RedisClient
 * @author <fl140125@gmail.com>
 * @package App\Http\Controllers\Utils
 */
class RedisClient extends Controller
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
     * @var $redisClient
     */
    protected $redisClient;
    /**
     * @var static $instance
     */
    protected static $instance;

    /**
     * @return static
     */
    public static function getInstance()
    {
        if (!self::$instance instanceof self) {
            return self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * RedisClient constructor.
     */
    public function __construct()
    {
        $this->redisClient = new \Redis();
        $this->host = config('app.redis_host');
        $this->port = config('app.redis_port');
        $this->password = config('app.redis_password');
        $this->redisClient->connect($this->host, $this->port);
        if (!empty($this->password)) {
            try {
                $this->redisClient->auth($this->password);
            } catch (\Exception $exception) {
                echo $exception->getMessage();
            }
        }
        return $this->redisClient;
    }

    /**
     * 数据存储 （Redis 字符串(String)）
     * @param $key
     * @param $value
     * @param array $timeout
     * @return bool
     * @throws RedisException
     */
    public function setValue($key, $value, array $timeout = ['EX' => 0])
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
     * 数据存储（Redis 集合(Set)）
     * @param $key
     * @param $value
     * @return int
     */
    public function sAdd($key, $value)
    {
        return $this->redisClient->sAdd($key, $value);
    }

    /**
     * 数据获取（Redis 集合(Set)）
     * @param $key
     * @return array
     * @throws RedisException
     */
    public function sMembers($key)
    {
        return $this->redisClient->sMembers($key);
    }

    /**
     * 判读数据是否存在（Redis 集合(Set)）
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
     * 数据删除（Redis 集合(Set)）
     * @param $key
     * @param $value
     * @return int
     */
    public function sRem($key, $value)
    {
        return $this->redisClient->sRem($key, $value);
    }

    /**
     * 删除指定的键。如果键不存在，则将其忽略。
     * @param $key
     * @return int
     * @throws RedisException
     */
    public function del($key)
    {
        return $this->redisClient->del($key);
    }

    /**
     * 数据添加（列表头部 Redis 列表(List)）
     * @param $key
     * @param $value
     * @return bool|int
     * @throws RedisException
     */
    public function lPush($key, $value)
    {
        return $this->redisClient->lPush($key, $value);
    }

    /**
     * 移除并返回列表的第一个元素（ Redis 列表(List)）
     * @param $key
     * @return bool|int
     * @throws RedisException
     */
    public function lPop($key)
    {
        return $this->redisClient->lPop($key);
    }

    /**
     * 数据添加（列表尾部 Redis 列表(List)）
     * @param $key
     * @param $value
     * @return bool|int
     * @throws RedisException
     */
    public function rPush($key, $value)
    {
        return $this->redisClient->rPush($key, $value);
    }

    /**
     * 移除并返回列表的最末元素（ Redis 列表(List)）
     * @param $key
     * @return bool|int
     * @throws RedisException
     */
    public function rPop($key)
    {
        return $this->redisClient->rPop($key);
    }

    /**
     * 返回列表中指定区间内的元素（ Redis 列表(List)）
     * @param $key
     * @param $start
     * @param $num
     * @return array
     * @throws RedisException
     */
    public function lRange($key, $start, $num)
    {
        return $this->redisClient->lRange($key, $start, $num);
    }

    /**
     * Redis Hgetall 命令用于返回哈希表中，所有的字段和值。（ Redis 哈希 (hash)）
     * @param $key
     * @return array
     * @throws RedisException
     */
    public function hGetAll($key)
    {
        return $this->redisClient->hGetAll($key);
    }

    /**
     * 命令用于为哈希表中的字段值加上指定增量值。（ Redis 哈希 (hash)）
     * @param $from
     * @param $to
     * @return int
     * @throws RedisException
     */
    public function hIncrBy($from, $to)
    {
        return $this->redisClient->hIncrBy($to, $from, 1);
    }

    /**
     * 命令用于删除哈希表 key 中的一个或多个指定字段，不存在的字段将被忽略。（ Redis 哈希 (hash)）
     * @param $from
     * @param $to
     * @return bool|int
     * @throws RedisException
     */
    public function hDel($from, $to)
    {
        return $this->redisClient->hDel($to, $from);
    }

    /**
     * 获取所有的Key
     * @param $pattern
     * @return array
     * @throws RedisException
     */
    public function keys($pattern)
    {
        return $this->redisClient->keys($pattern);
    }

    /**
     * 获取聊天记录长度
     * @param $key
     * @return int
     * @throws RedisException
     */
    public function lLen($key)
    {
        return $this->redisClient->lLen($key);
    }
}

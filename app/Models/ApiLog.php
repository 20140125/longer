<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class ApiLog
 * @author <fl140125@gmail.com>
 * @package App\Models
 */
class ApiLog extends Model
{
    /**
     * @var string $table
     */
    public $table = 'os_api_log';
    /**
     * @var static $instance
     */
    protected static $instance;

    /**
     * @return ApiLog
     */
    public static function getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new static();
        }
        return self::$instance;
    }
    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    /**
     * TODO: 查询记录
     * @param string $field
     * @param int $value
     * @param string $op
     * @param array $column
     * @return Collection
     */
    public function getResult(string $field, int $value, string $op = '=', array $column = ['*'])
    {
        return DB::table($this->table)->where($field, $op, $value)->orderBy('id', 'desc')->limit(10)->get($column);
    }
    /**
     * TODO: 添加记录
     * @param array $data
     * @return bool
     */
    public function addResult(array $data)
    {
        return DB::table($this->table)->insertGetId($data);
    }

    /**
     *  TODO: 删除api日志
     * @param string $filed
     * @param int $value
     * @return int
     */
    public function deleteResult(string $filed, int $value)
    {
        return DB::table($this->table)->where($filed, $value)->delete();
    }
}

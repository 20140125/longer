<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
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
     * @var $instance
     */
    protected static $instance;

    /**
     * @return ApiLog
     */
    static public function getInstance()
    {
        if (!self::$instance instanceof self){
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
     * @param $field
     * @param $value
     * @param string $op
     * @param array $column
     * @return Model|Builder|null|object
     */
    public function getResult($field, $value,$op='=', $column = ['*'])
    {
        $result = DB::table($this->table)->where($field,$op,$value)->orderBy('id','desc')->limit(10)->get($column);
        return $result;
    }
    /**
     * TODO: 添加记录
     * @param $data
     * @return bool
     */
    public function addResult($data)
    {
        $result = DB::table($this->table)->insertGetId($data);
        return $result;
    }

    /**
     * TODO: 删除api日志
     * @param $filed
     * @param $value
     * @return int
     */
    public function deleteResult($filed,$value)
    {
        $result = DB::table($this->table)->where($filed,$value)->delete();
        return $result;
    }
}

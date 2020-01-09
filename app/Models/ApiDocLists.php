<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Class ApiDocLists
 * @author <fl140125@gmail.com>
 * @package App\Models
 */
class ApiDocLists extends Model
{
    /**
     * @var string $table
     */
    public $table = 'os_api_doc';
    /**
     * @var $instance
     */
    protected static $instance;

    /**
     * @return ApiDocLists
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
     * TODO: 查询一条记录
     * @param string $field
     * @param int $value
     * @param string $op
     * @param array $column
     * @return Model|Builder|object|null
     */
    public function getResult(string $field, int $value,string $op='=', array $column = ['*'])
    {
        return DB::table($this->table)->where($field,$op,$value)->first($column);
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
     * TODO: 更新一条数据
     * @param array $data
     * @param string $field
     * @param int $value
     * @param string $op
     * @return int
     */
    public function updateResult(array $data,string $field,int $value,string $op='=')
    {
        return DB::table($this->table)->where($field,$op,$value)->update($data);
    }

    /**
     * TODO: 删除一条数据
     * @param string $field
     * @param int $value
     * @return int
     */
    public function deleteResult(string $field,int $value)
    {
        return DB::table($this->table)->where($field,$value)->delete();
    }
}

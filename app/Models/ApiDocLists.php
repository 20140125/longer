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
     * @param $field
     * @param $value
     * @param string $op
     * @param array $column
     * @return Model|Builder|null|object
     */
    public function getResult($field, $value,$op='=', $column = ['*'])
    {
        $result = DB::table($this->table)->where($field,$op,$value)->first($column);
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
     * TODO: 更新一条数据
     * @param $data
     * @param $field
     * @param $value
     * @param string $op
     * @return int
     */
    public function updateResult($data,$field,$value,$op='=')
    {
        $result = DB::table($this->table)->where($field,$op,$value)->update($data);
        return $result;
    }

    /**
     * TODO: 删除一条数据
     * @param $field
     * @param $value
     * @return int
     */
    public function deleteResult($field,$value)
    {
        $result = DB::table($this->table)->where($field,$value)->delete();
        return $result;
    }
}
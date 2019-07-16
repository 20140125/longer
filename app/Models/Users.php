<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class Users extends Model
{
    protected static $tableName = 'os_users';
    private static $instance;
    protected static $code;

    private function __clone()
    {
        // TODO: Implement __clone() method.
    }
    static public function getInstance()
    {
        if (!self::$instance instanceof self){
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * 查询一条记录
     * @param $field
     * @param $value
     * @param string $op
     * @param array $column
     * @return Model|Builder|null|object
     */
    public function getResult($field, $value,$op='=', $column = ['*'])
    {
        $result = DB::table(self::$tableName)->where($field,$op,$value)->first($column);
        return $result;
    }
    /**
     * 添加记录
     * @param $data
     * @return bool
     */
    public function addResult($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s');
        $result = DB::table(self::$tableName)->insertGetId($data);
        return $result;
    }

    /**
     * 更新一条数据
     * @param $data
     * @param $field
     * @param $value
     * @param $op
     * @return int
     */
    public function updateResult($data,$field,$value,$op='=')
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $result = DB::table(self::$tableName)->where($field,$value,$op)->update($data);
        return $result;
    }

    /**
     * 删除一条数据
     * @param $field
     * @param $value
     * @param $op
     * @return int
     */
    public function deleteResult($field,$value,$op='=')
    {
        $result = DB::table(self::$tableName)->whereIn($field,$value,$op)->delete();
        return $result;
    }
}

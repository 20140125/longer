<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Log extends Model
{
    protected static $tableName = 'os_system_log';
    protected static $instance;

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
     * 日志列表
     * @param $page
     * @param $limit
     * @param $ctime
     * @return Collection
     */
    public function getLists($page,$limit,$ctime)
    {
        $where =[];
        if (!empty($ctime)){
            $where[] = ['created_at','<=',$ctime];
        }
        $result['data'] =  DB::table(self::$tableName)->where($where)->limit($limit)->offset($limit*($page-1))->orderBy('id','desc')->get();
        $result['total'] = DB::table(self::$tableName)->where($where)->count();
        return $result;
    }
    /**
     * 添加记录
     * @param $data
     * @return bool
     */
    public function addResult($data)
    {
        $result = DB::table(self::$tableName)->insertGetId($data);
        return $result;
    }
    /**
     * 更新一条数据
     * @param $data
     * @param $field
     * @param $value
     * @param string $op
     * @return int
     */
    public function updateResult($data,$field,$value,$op='=')
    {
        $result = DB::table(self::$tableName)->where($field,$op,$value)->update($data);
        return $result;
    }

    /**
     * 删除一条数据
     * @param $field
     * @param $value
     * @param string $op
     * @return int
     */
    public function deleteResult($field,$value,$op='in')
    {
        $result = 0;
        switch ($op){
            case '=':
                $result = DB::table(self::$tableName)->where($field,$value)->delete();
                break;
            case 'in':
                $result = DB::table(self::$tableName)->whereIn($field,$value)->delete();
                break;
        }
        return $result;
    }

}

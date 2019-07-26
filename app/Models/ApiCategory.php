<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ApiCategory extends Model
{
    protected static $tableName = 'os_api_category';

    protected static $instance;

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
     * todo：查询一条记录
     * @param $field
     * @param $value
     * @param string $op
     * @param array $column
     * @return Model|Builder|null|object
     */
    public function getResult($field, $value,$op='=', $column = ['*'])
    {
        $result = DB::table(self::$tableName)->where($field,$op,$value)-> first($column);
        return $result;
    }

    /**
     * 获取二级分类
     * @return Collection
     */
    public function getResultListsLevel2()
    {
        $result = DB::table(self::$tableName)->orderBy('path')->get(['name','id','pid','level']);
        return $result;
    }
    /**
     * 添加记录
     * @param $data
     * @return bool
     */
    public function addResult($data)
    {
        $id = DB::table(self::$tableName)->insertGetId($data);
        $parent_result = self::getInstance()->getResult('id',$data['pid']);
        $data['path'] = $id;
        $data['level'] = 0;
        if (!empty($parent_result)){
            $data['path'] = $parent_result->path.'-'.$id;
            $data['level'] = substr_count($data['path'],'-');
        }
        $result = DB::table(self::$tableName)->where('id',$id)->update($data);
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
        $parent_result = self::getInstance()->getResult($field,$value,$op);
        if (!empty($parent_result)){
            if (!empty($parent_result->path)){
                $data['path'] = $parent_result->path.'-'.$data['id'];
            } else {
                $data['path'] = $data['id'];
            }
            $data['level'] = substr_count($data['path'],'-');
        }
        $result = DB::table(self::$tableName)->where($field,$op,$data['id'])->update($data);
        return $result;
    }

    /**
     * 删除一条数据
     * @param $field
     * @param $value
     * @return int
     */
    public function deleteResult($field,$value)
    {
        $result = DB::table(self::$tableName)->where($field,$value)->delete();
        return $result;
    }
}

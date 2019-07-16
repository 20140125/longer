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
     * 查询一条记录
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
        $result = DB::table(self::$tableName)->orderBy('path')->get(['name as label','id as value','pid','level']);
        return $result;
    }

    /**
     * 权限分页列表
     * @param $name
     * @param $pid
     * @param $page
     * @param $limit
     * @return Collection
     */
    public function getResultLists($name,$pid,$page,$limit=10)
    {
        $where[] = array('id','>',0);
        if (!empty($name)){
            $where[] = ['name','like','%'.$name.'%'];
        }
        if (!empty($pid)){
            $where[] = ['pid','=',$pid];
            $result['data'] = DB::table(self::$tableName)->where($where)->orWhere('id',$pid)->offset($limit*($page-1))->limit($limit)->orderBy('path')->get();
        }else{
            $result['data'] = DB::table(self::$tableName)->where($where)->offset($limit*($page-1))->limit($limit)->orderBy('path')->get();
        }
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
            if (!strstr($parent_result->path,strval($value))){
                $data['path'] = $parent_result->path.'-'.$value;
                $data['level'] = substr_count($data['path'],'-');
            }
        }
        $result = DB::table(self::$tableName)->where($field,$op,$value)->update($data);
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

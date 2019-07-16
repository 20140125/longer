<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AuthRule extends Model
{
    protected static $tableName='os_auth_rules';

    private static $instance;

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
    public function getResult($field, $value, $op='=',$column = ['*'])
    {
        switch ($op){
            case 'in':
                $result = DB::table(self::$tableName)->whereIn($field,$value)->get($column);
                break;
            default:
                $result = DB::table(self::$tableName)->where($field,$op,$value)->first($column);
                break;
        }
        return $result;
    }

    /**
     * 查询一级显示的分类
     * @param string $status
     * @param int $level
     * @return Collection
     */
    public function getResult2($status='1',$level=0)
    {
        $where['status'] = $status;
        if (!empty($level)){
            $where[] = ['level','<',$level];
        }
        $result = DB::table(self::$tableName)->orderBy('path','asc')->where($where)->get();
        return $result;
    }

    /**
     * 权限列表
     * @param int $pid
     * @param array $ids
     * @return Collection
     */
    public function getAuthTree($pid = 0,$ids=[])
    {
        $where[] = array('pid','=',$pid);
        $where[] = array('id','>',0);
        $where[] = array('status','=',1);;
        if (empty($ids)){
            $result = DB::table(self::$tableName)->where($where)->get();
            foreach ($result as &$item){
                $item->__children = self::getInstance()->getAuthTree($item->id,$ids);
            }
        }else{
            $result = DB::table(self::$tableName)->where($where)->whereIn('id',$ids)->get();
            foreach ($result as &$item){
                $item->__children = self::getInstance()->getAuthTree($item->id,$ids);
            }
        }
        return $result;
    }

     /**
     * todo：权限列表
     * @return Collection
     */
    public function getAuthList()
    {
        $where[] = array('id','>',0);
        $result = DB::table(self::$tableName)->where($where)->orderBy('path')->get(['id as key','name as label']);
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
    public function getAuthLists($name,$pid,$page,$limit)
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
     * @param string $op
     * @return int
     */
    public function deleteResult($field,$value,$op='=')
    {
        $result = DB::table(self::$tableName)->where($field,$op,$value)->delete();
        return $result;
    }

}

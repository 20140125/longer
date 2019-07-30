<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class Rule
 * @author <fl140125@gmail.com>
 * @package App\Models
 */
class Rule extends Model
{
    /**
     * @var string $table
     */
    protected $table='os_rule';
    /**
     * @var $instance
     */
    private static $instance;

    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    /**
     * @return Rule
     */
    static public function getInstance()
    {
        if (!self::$instance instanceof self){
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * TODO：查询一条记录
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
                $result = DB::table($this->table)->whereIn($field,$value)->get($column);
                break;
            default:
                $result = DB::table($this->table)->where($field,$op,$value)->first($column);
                break;
        }
        return $result;
    }

    /**
     * TODO：查询一级显示的分类
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
        $result = DB::table($this->table)->orderBy('path','asc')->where($where)->get();
        return $result;
    }

    /**
     * TODO：权限列表
     * @param array $ids
     * @return Collection
     */
    public function getAuthTree($ids = [])
    {
        $where['status'] = 1;
        $where[] = ['level','<',2];
        if (empty($ids)){
            $result =  DB::table($this->table)->where($where)->get();
        }else{
            $result = DB::table($this->table)->where($where)->whereIn('id',$ids)->get();
        }
        return $result;
    }

     /**
     * TODO：权限列表
     * @return Collection
     */
    public function getAuthList()
    {
        $where[] = array('id','>',0);
        $result = DB::table($this->table)->where($where)->orderBy('path')->get(['id as key','name as label']);
        return $result;
    }


    /**
     * TODO：权限分页列表
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
            $result['data'] = DB::table($this->table)->where($where)->orWhere('id',$pid)->offset($limit*($page-1))->limit($limit)->orderBy('path')->get();
            $result['total'] = DB::table($this->table)->where($where)->orWhere('id',$pid)->count();
        }else{
            $result['data'] = DB::table($this->table)->where($where)->offset($limit*($page-1))->limit($limit)->orderBy('path')->get();
            $result['total'] = DB::table($this->table)->where($where)->count();
        }
        return $result;
    }

    /**
     * TODO 添加记录
     * @param $data
     * @return bool
     */
    public function addResult($data)
    {
        $id = DB::table($this->table)->insertGetId($data);
        $parent_result = $this->getResult('id',$data['pid']);
        $data['path'] = $id;
        $data['level'] = 0;
        if (!empty($parent_result)){
            $data['path'] = $parent_result->path.'-'.$id;
            $data['level'] = substr_count($data['path'],'-');
        }
        $result = DB::table($this->table)->where('id',$id)->update($data);
        return $result;
    }

    /**
     * TODO 更新一条数据
     * @param $data
     * @param $field
     * @param $value
     * @param string $op
     * @return int
     */
    public function updateResult($data,$field,$value,$op='=')
    {
        $parent_result = $this->getResult($field,$value,$op);
        if (!empty($parent_result)){
            if (!empty($parent_result->path)){
                $data['path'] = $parent_result->path.'-'.$data['id'];
            } else {
                $data['path'] = $data['id'];
            }
            $data['level'] = substr_count($data['path'],'-');
        }
        $result = DB::table($this->table)->where($field,$op,$data['id'])->update($data);
        return $result;
    }

    /**
     * TODO 删除一条数据
     * @param $field
     * @param $value
     * @param string $op
     * @return int
     */
    public function deleteResult($field,$value,$op='=')
    {
        $result = DB::table($this->table)->where($field,$op,$value)->delete();
        return $result;
    }

}

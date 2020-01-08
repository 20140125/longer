<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class Role
 * @author <fl140125@gmail.com>
 * @package App\Models
 */
class Role extends Model
{
    /**
     * @var string $table
     */
    public $table = 'os_role';
    /**
     * @var static $instance
     */
    private static $instance;

    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    /**
     * @return Role
     */
    static public function getInstance()
    {
        if (!self::$instance instanceof self){
            self::$instance = new static();
        }
        return self::$instance;
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
        return DB::table($this->table)->where($field,$op,$value)->first($column);
    }

    /**
     * TODO: 获取角色列表
     * @param int $status
     * @param array $column
     * @return Collection
     */
    public function getResult2($status = 0,$column = ['*'])
    {
        $where=[];
        if (!empty($status)){
            $where[] = array('status','=',$status);
        }
        return DB::table($this->table)->where($where)->get($column);
    }

    /**
     *  TODO: 获取角色列表
     * @param $user
     * @param $page
     * @param $limit
     * @param int $status
     * @param array $column
     * @return Collection
     */
    public function getResultLists($user,$page,$limit,$status = 0,$column = ['*'])
    {
        $where=[];
        if (!empty($status)){
            $where[] = array('status','=',$status);
        }
        if (!in_array($user->role_id,[1])){
            $where[] = ['id',$user->role_id];
        }
        $result['data'] = DB::table($this->table)->offset($limit*($page-1))->limit($limit)->where($where)->get($column);
        $result['total'] = DB::table($this->table)->where($where)->count();
        return $result;
    }

    /**
     * TODO: 添加记录
     * @param $data
     * @return bool
     */
    public function addResult($data)
    {
        return DB::table($this->table)->insertGetId($data);
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
        return DB::table($this->table)->where($field,$op,$value)->update($data);
    }

    /**
     * TODO: 删除一条数据
     * @param $field
     * @param $value
     * @param string $op
     * @return int
     */
    public function deleteResult($field,$value,$op='=')
    {
        return DB::table($this->table)->where($field,$op,$value)->delete();
    }
}

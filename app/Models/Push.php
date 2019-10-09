<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class Push
 * @author <fl1401025@gmail.com>
 * @package App\Models
 */
class Push extends Model
{
    /**
     * @var string $table
     */
    public $table = 'os_push';
    /**
     * @var static $instance
     */
    protected static $instance;

    /**
     * @return Push
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
     * TODO：获取列表
     * @param $where
     * @return Collection
     */
    public function getCommandPush($where)
    {
        return DB::table($this->table)->where($where)->get();
    }
    /**
     * TODO:推送列表
     * @param $page
     * @param $limit
     * @param $user
     * @param string $state
     * @param string $status
     * @return mixed
     */
    public function getResultLists($page,$limit,$user,$state='',$status='')
    {
        $where = [];
        if (!empty($state)) {
            $where[] = ['state',$state];
        }
        if (!empty($status)) {
            $where[] = ['status',$status];
        }
        if (!in_array($user->username,['admin'])){
            $where[] = ['uid',md5($user->username)];
        }
        $result['data'] = DB::table($this->table)->where($where)->orderByDesc('created_at')->offset($limit*($page-1))->limit($limit)->get();
        $result['total'] = DB::table($this->table)->where($where)->count();
        return $result;
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

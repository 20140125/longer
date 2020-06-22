<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\Integer;

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
     * @param array $where
     * @return Collection
     */
    public function getCommandPush(array $where)
    {
        return DB::table($this->table)->where($where)->get();
    }
    /**
     * TODO:推送列表
     * @param int $page
     * @param int $limit
     * @param $user
     * @param string $state
     * @param int $status
     * @return mixed
     */
    public function getResultLists(int $page,int $limit,$user,string $state='',int $status=0)
    {
        $where = [];
        if (!empty($state)) {
            $where[] = ['state',$state];
        }
        if (!empty($status)) {
            $where[] = ['status',$status];
        }
        if (!in_array($user->role_id,[1])){
            $where[] = ['uid',empty($user->uuid) ? '' : $user->uuid];
        }
        $result['data'] = DB::table($this->table)->where($where)->orderByDesc('id')->offset($limit*($page-1))->limit($limit)->get();
        $result['total'] = DB::table($this->table)->where($where)->count();
        return $result;
    }

    /**
     * TODO: 查询一条记录
     * @param string $field
     * @param string  $value
     * @param string $op
     * @param array $column
     * @return Model|Builder|null|object
     */
    public function getResult(string $field, string $value,string $op='=', array $column = ['*'])
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
    public function updateResult(array $data,string $field,int $value,$op='=')
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

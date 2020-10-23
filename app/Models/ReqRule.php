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
class ReqRule extends Model
{
    /**
     * @var string $table
     */
    protected $table='os_req_rule';
    /**
     * @var $instance
     */
    private static $instance;

    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    /**
     * @return ReqRule
     */
    static public function getInstance()
    {
        if (!self::$instance instanceof self){
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * TODO：获取列表
     * @param array $where
     * @return Collection
     */
    public function getCommandRule($where = [])
    {
        return DB::table($this->table)->where($where)->get();
    }

    /**
     * TODO：查询记录
     * @param array|string $field
     * @param int $value
     * @param string $op
     * @param array $column
     * @return Model|Builder|null|object
     */
    public function getResult($field, int $value=0, string $op='=',array $column = ['*'])
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
     * TODO：请求授权列表
     * @param int $page
     * @param int $limit
     * @param $user
     * @return mixed
     */
    public function getResultLists(int $page,int $limit,$user)
    {
        $where = [];
        if (!in_array($user->role_id,[1])){
            $where[] = ['user_id',$user->id];
        }
        $result['data'] = DB::table($this->table)->limit($limit)->where($where)->offset($limit*($page-1))->orderBy('expires')->get();
        $result['total'] = DB::table($this->table)->where($where)->count();
        return $result;
    }

    /**
     * TODO 添加记录
     * @param array $data
     * @return bool
     */
    public function addResult(array $data)
    {
        $data['created_at'] = time();
        return DB::table($this->table)->insert($data);
    }

    /**
     * TODO 更新一条数据
     * @param array $data
     * @param $field
     * @param int $value
     * @param string $op
     * @return int
     */
    public function updateResult(array $data ,$field,int $value=0,string $op='=')
    {
        $data['updated_at'] = time();
        return DB::table($this->table)->where($field,$op,$value)->update($data);
    }

    /**
     * TODO 删除一条数据
     * @param string $field
     * @param int $value
     * @param string $op
     * @return int
     */
    public function deleteResult(string $field,int $value,string $op='=')
    {
        return DB::table($this->table)->where($field,$op,$value)->delete();
    }

}

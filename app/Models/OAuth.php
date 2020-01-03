<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class OAuth
 * @author <fl140125@gmail.com>
 * @package App\Models
 */
class OAuth extends Model
{
    /**
     * @var string $table
     */
    public $table = 'os_oauth';
    /**
     * @var static $instance
     */
    protected static $instance;
    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    /**
     * @return OAuth
     */
    static public function getInstance()
    {
        if (!self::$instance instanceof self){
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * TODO:获取授权列表
     * @param $page
     * @param $limit
     * @param $user
     * @return mixed
     */
    public function getResultLists($page,$limit,$user)
    {
        $where = [];
        if (!in_array($user->username,['admin'])){
            $where[] = ['uid',$user->id];
        }
        $result['data'] = DB::table($this->table)->limit($limit)->where($where)->orderBy('updated_at','desc')->offset($limit*($page-1))->get();
        $result['total'] = DB::table($this->table)->where($where)->count();
        return $result;
    }

    /**
     * TODO：获取授权列表
     * @param $where
     * @param array $column
     * @return Collection
     */
    public function getOauthLists($where,$column = ['*'])
    {
        return DB::table($this->table)->where($where)->get($column);
    }

    /**
     * TODO:授权图表
     * @return mixed
     */
    public function oauthChart()
    {
        $result['oauth_type'] = DB::table($this->table)->groupBy('oauth_type')
            ->select(DB::raw('oauth_type,count(oauth_type) as count'))
            ->get();
        return $result;
    }
    /**
     * TODO:查询一条记录
     * @param $field
     * @param $value
     * @param string $op
     * @param array $column
     * @return Model|Builder|null|object
     */
    public function getResult($field, $value='',$op='=', $column = ['*'])
    {
        $result = DB::table($this->table)->where($field,$op,$value)->first($column);
        return $result;
    }
    /**
     * TODO:添加记录
     * @param $data
     * @return bool
     */
    public function addResult($data)
    {
        $data['created_at'] = time();
        $result = DB::table($this->table)->insertGetId($data);
        return $result;
    }
    /**
     * TODO:更新一条数据
     * @param $data
     * @param $field
     * @param $value
     * @param string $op
     * @return int
     */
    public function updateResult($data,$field,$value=null,$op='=')
    {
        $data['updated_at'] = time();
        $result = DB::table($this->table)->where($field,$op,$value)->update($data);
        return $result;
    }

    /**
     * TODO:删除一条数据
     * @param $field
     * @param $value
     * @return int
     */
    public function deleteResult($field,$value)
    {
        $result = DB::table($this->table)->whereIn($field,$value)->delete();
        return $result;
    }
}

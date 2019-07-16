<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class UserMusic extends Model
{
    protected static $tableName='os_user_music';

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
     * @param string $field
     * @param string $value
     * @param string $op
     * @param array $column
     * @return Model|Builder|object|null
     */
    public function getResult($field, $value='',$op='=', $column = ['*'])
    {
        $result = DB::table(self::$tableName)->where($field,$op,$value)->first($column);
        return $result;
    }

    /**
     * 播放历史
     * @param $page
     * @param $limit
     * @param $openid
     * @param string $status
     * @return mixed
     */
    public function getResultLists($page,$limit,$openid,$status='')
    {
        $where[] = array('openid','=',$openid);
        if (!empty($status)){
            $where[] = array('status','=',$status);
        }
        $result['data'] = DB::table(self::$tableName)->where($where)->orderBy('updated_at','desc')->offset($limit*($page-1))->limit($limit)->get();
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
        $data['created_at'] = date("Y-m-d H:i:s",time());
        $data['updated_at'] = date("Y-m-d H:i:s",time());
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
    public function updateResult($data,$field,$value='',$op='=')
    {
        $data['updated_at'] = date("Y-m-d H:i:s",time());
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
        $result = DB::table(self::$tableName)->whereIn($field,$value)->delete();
        return $result;
    }
}

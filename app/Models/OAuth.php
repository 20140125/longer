<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class OAuth extends Model
{
    protected static $tableName = 'os_oauth';
    protected static $instance;
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
     * 获取授权列表
     * @param $page
     * @param $limit
     * @return mixed
     */
    public function getResultLists($page,$limit)
    {
        $result['data'] = DB::table(self::$tableName)->limit($limit)->orderBy('updated_at','desc')->offset($limit*($page-1))->get();
        $result['total'] = DB::table(self::$tableName)->count();
        return $result;
    }

    /**
     * 授权图表
     * @return mixed
     */
    public function oauthChart()
    {
        $result['oauth_type'] = DB::table(self::$tableName)->groupBy('oauth_type')
            ->select(DB::raw('oauth_type,count(oauth_type) as count'))
            ->get();
        return $result;
    }
    /**
     * 查询一条记录
     * @param $field
     * @param $value
     * @param string $op
     * @param array $column
     * @return Model|Builder|null|object
     */
    public function getResult($field, $value='',$op='=', $column = ['*'])
    {
        $result = DB::table(self::$tableName)->where($field,$op,$value)->first($column);
        return $result;
    }
    /**
     * 添加记录
     * @param $data
     * @return bool
     */
    public function addResult($data)
    {
        $data['created_at'] = date('Y-m-d H:i:s',time());
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
    public function updateResult($data,$field,$value=null,$op='=')
    {
        $data['updated_at'] = date('Y-m-d H:i:s',time());
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

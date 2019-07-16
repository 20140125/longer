<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class UserSearch extends Model
{
    protected static $tableName='os_music_user_search';

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
    public function getResult($field, $value='',$op='=', $column = ['*'])
    {
        $result = DB::table(self::$tableName)->where($field,$op,$value)->first($column);
        return $result;
    }

    /**
     * 音乐搜索图表
     * @return Collection
     */
    public function userSearchChart()
    {
        $result = DB::table(self::$tableName)->get(['s','count']);
        return $result;
    }

    /**
     * 获取搜索频率最高的词语
     * @return Collection
     */
    public function getSearchCount()
    {
        $result = DB::table(self::$tableName)->orderBy('count','desc')->limit(15)->get(['s']);
        return $result;
    }

    /**
     * @param $page
     * @param int $limit
     * @return mixed
     */
    public function getResultLists($page,$limit=10)
    {

        $result['data'] = DB::table(self::$tableName)->offset($limit*($page-1))->limit($limit)->get();
        $result['total'] = DB::table(self::$tableName)->count();
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

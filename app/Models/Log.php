<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class Log
 * @author <fl140125@gmail.com>
 * @package App\Models
 */
class Log extends Model
{
    /**
     * @var string $table
     */
    public $table = 'os_system_log';
    /**
     * @var static $instance
     */
    protected static $instance;

    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    /**
     * @return Log
     */
    static public function getInstance()
    {
        if (!self::$instance instanceof self){
            self::$instance = new static();
        }
        return self::$instance;
    }
    /**
     * TODO:日志列表
     * @param $page
     * @param $limit
     * @param $ctime
     * @return Collection
     */
    public function getLists($page,$limit,$ctime)
    {
        $where =[];
        if (!empty($ctime)){
            $where[] = ['created_at','<=',$ctime];
        }
        $result['data'] =  DB::table($this->table)->where($where)->limit($limit)->offset($limit*($page-1))->orderBy('id','desc')->get();
        $result['total'] = DB::table($this->table)->where($where)->count();
        return $result;
    }
    /**
     * TODO:添加记录
     * @param $data
     * @return bool
     */
    public function addResult($data)
    {
        $result = DB::table($this->table)->insertGetId($data);
        return $result;
    }
    /**
     * TODO:删除一条数据
     * @param $field
     * @param $value
     * @param string $op
     * @return int
     */
    public function deleteResult($field,$value,$op='in')
    {
        $result = 0;
        switch ($op){
            case '=':
                $result = DB::table($this->table)->where($field,$value)->delete();
                break;
            case 'in':
                $result = DB::table($this->table)->whereIn($field,$value)->delete();
                break;
        }
        return $result;
    }

}

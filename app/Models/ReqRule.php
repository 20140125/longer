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
     * TODO：查询记录
     * @param $field
     * @param string $value
     * @param string $op
     * @param array $column
     * @return Model|Builder|null|object
     */
    public function getResult($field, $value='', $op='=',$column = ['*'])
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
     * @param $page
     * @param $limit
     * @return mixed
     */
    public function getResultLists($page,$limit)
    {
        $result['data'] = DB::table($this->table)->limit($limit)->offset($limit*($page-1))->get();
        $result['total'] = DB::table($this->table)->count();
        return $result;
    }

    /**
     * TODO 添加记录
     * @param $data
     * @return bool
     */
    public function addResult($data)
    {
        $data['created_at'] = time();
        $result = DB::table($this->table)->insert($data);
        return $result;
    }

    /**
     * TODO 更新一条数据
     * @param $data
     * @param $field
     * @param string $value
     * @param string $op
     * @return int
     */
    public function updateResult($data,$field,$value='',$op='=')
    {
        $data['updated_at'] = time();
        $result = DB::table($this->table)->where($field,$op,$value)->update($data);
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

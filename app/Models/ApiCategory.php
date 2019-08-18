<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class ApiCategory
 * @author <fl140125@gmail.com>
 * @package App\Models
 */
class ApiCategory extends Model
{
    /**
     * @var string $table
     */
    public $table = 'os_api_category';
    /**
     * @var $instance
     */
    protected static $instance;

    /**
     * @return ApiCategory
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
     * TODO:查询一条记录
     * @param $field
     * @param $value
     * @param string $op
     * @param array $column
     * @return Model|Builder|null|object
     */
    public function getResult($field, $value,$op='=', $column = ['*'])
    {
        $result = DB::table($this->table)->where($field,$op,$value)-> first($column);
        return $result;
    }

    /**
     * TODO: 获取二级分类
     * @return Collection
     */
    public function getResultListsLevel2()
    {
        $result = DB::table($this->table)->orderBy('path')->get(['name','id','pid','level']);
        return $result;
    }

    /**
     * TODO:添加记录
     * @param $data
     * @return int
     */
    public function addResult($data)
    {
        $id = DB::table($this->table)->insertGetId($data);
        $parent_result = $this->getResult('id',$data['pid']);
        $data['path'] = $id;
        $data['level'] = 0;
        if (!empty($parent_result)){
            $data['path'] = $parent_result->path.'-'.$id;
            $data['level'] = substr_count($data['path'],'-');
        }
        $result = DB::table($this->table)->where('id',$id)->update($data);
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
    public function updateResult($data,$field,$value,$op='=')
    {
        $parent_result = $this->getResult($field,$value,$op);
        if (!empty($parent_result) && !empty($data['path'])) {
            if (!empty($parent_result->path)){
                $data['path'] = $parent_result->path.'-'.$data['id'];
            } else {
                $data['path'] = $data['id'];
            }
            $data['level'] = substr_count($data['path'],'-');
        }
        $result = DB::table($this->table)->where($field,$op,$data['id'])->update($data);
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
        $result = DB::table($this->table)->where($field,$value)->delete();
        return $result;
    }
}

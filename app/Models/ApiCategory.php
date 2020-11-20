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
     * @var static $instance
     */
    protected static $instance;

    /**
     * @return ApiCategory
     */
    public static function getInstance()
    {
        if (!self::$instance instanceof self) {
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
     * @param string $field
     * @param string $value
     * @param string $op
     * @param array $column
     * @return Model|Builder|object|null
     */
    public function getResult(string $field, string $value, string $op = '=', array $column = ['*'])
    {
        return DB::table($this->table)->where($field, $op, $value)-> first($column);
    }

    /**
     * TODO: 获取二级分类
     * @return Collection
     */
    public function getResultListsLevel2()
    {
        return DB::table($this->table)->orderBy('path')->get(['name','id','pid','level']);
    }

    /**
     * TODO:添加记录
     * @param array $data
     * @return int
     */
    public function addResult(array $data)
    {
        $id = DB::table($this->table)->insertGetId($data);
        $parent_result = $this->getResult('id', $data['pid']);
        $data['path'] = $id;
        $data['level'] = 0;
        if (!empty($parent_result)) {
            $data['path'] = $parent_result->path.'-'.$id;
            $data['level'] = substr_count($data['path'], '-');
        }
        return DB::table($this->table)->where('id', $id)->update($data);
    }

    /**
     * TODO:更新一条数据
     * @param array $data
     * @param string $field
     * @param int $value
     * @param string $op
     * @return int
     */
    public function updateResult(array $data, string $field, int $value, string $op = '=')
    {
        $parent_result = $this->getResult($field, $value, $op);
        if (!empty($parent_result) && !empty($data['path'])) {
            if (!empty($parent_result->path)) {
                $data['path'] = $parent_result->path.'-'.$data['id'];
            } else {
                $data['path'] = $data['id'];
            }
            $data['level'] = substr_count($data['path'], '-');
        }
        return DB::table($this->table)->where($field, $op, $data['id'])->update($data);
    }

    /**
     * TODO:删除一条数据
     * @param string $field
     * @param int $value
     * @return int
     */
    public function deleteResult(string $field, int $value)
    {
        return DB::table($this->table)->where($field, $value)->delete();
    }
}

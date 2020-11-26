<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class Auth
 * @author <fl140125@gmail.com>
 * @package App\Models
 */
class Auth extends Model
{
    /**
     * @var string $table
     */
    protected $table='os_rule';
    /**
     * @var static $instance
     */
    private static $instance;

    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    /**
     * @return Auth
     */
    public static function getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * TODO：查询一条记录
     * @param string $field
     * @param $value
     * @param string $op
     * @param array $column
     * @return Model|Builder|Collection|object|null
     */
    public function getResult(string $field, $value, string $op = '=', array $column = ['*'])
    {
        switch ($op) {
            case 'in':
                $result = DB::table($this->table)->whereIn($field, $value)->get($column);
                break;
            default:
                $result = DB::table($this->table)->where($field, $op, $value)->first($column);
                break;
        }
        return $result;
    }

    /**
     * TODO：根据层级查询分类
     * @param string $status
     * @param int $level
     * @return Collection
     */
    public function getResultListsByStatusAndLevel(string $status = '1', int $level = 0)
    {
        $where = [];
        if (!empty($level)) {
            $where[] = ['level','<',$level];
        }
        if (!empty($status)) {
            $where[] = ['status','=',$status];
        }
        return DB::table($this->table)->orderBy('path', 'asc')->where($where)->get();
    }

    /**
     * TODO：权限列表
     * @param array $ids
     * @return Collection
     */
    public function getAuthTree(array $ids = [])
    {
        $where['status'] = 1;
        $where[] = ['level','<',2];
        if (empty($ids)) {
            $result =  DB::table($this->table)->where($where)->get();
        } else {
            $result = DB::table($this->table)->where($where)->whereIn('id', $ids)->get();
        }
        return $result;
    }
    /**
     * TODO：权限分页列表
     * @param array $column
     * @param string $id
     * @return Collection
     */
    public function getAuthLists(array $column = ['*'], string $id = '')
    {
        $where=[];
        if (!empty($id) || (int)$id == 0) {
            $where[] = ['pid',$id];
        }
        return DB::table($this->table)->where($where)->get($column);
    }

    /**
     * TODO 添加记录
     * @param array $data
     * @return bool
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
     * TODO 更新一条数据
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
        unset($data['hasChildren']);
        return DB::table($this->table)->where($field, $op, $data['id'])->update($data);
    }

    /**
     * TODO 删除一条数据
     * @param string $field
     * @param int $value
     * @param string $op
     * @return int
     */
    public function deleteResult(string $field, int $value, string $op = '=')
    {
        return DB::table($this->table)->where($field, $op, $value)->delete();
    }
}

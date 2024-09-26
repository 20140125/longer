<?php

namespace App\Models\Api\v1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Base extends Model
{
    use HasFactory;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    /**
     * 获取一条记录
     * @param $table
     * @param $where
     * @param string[] $columns
     * @return Model|Builder|object|null
     */
    protected function getResult($table, $where, $columns = ['*'])
    {
        return DB::table($table)->where($where)->first($columns);
    }

    /**
     * 删除一条记录
     * @param $table
     * @param $where
     * @return int
     */
    protected function remove($table, $where)
    {
        return DB::table($table)->where($where)->delete();
    }

    /**
     * 保存数据
     * @param $table
     * @param $where
     * @param $form
     * @return int
     */
    protected function updateResult($table, $where, $form)
    {
        return DB::table($table)->where($where)->update($form);
    }

    /**
     * 添加数据
     * @param $table
     * @param $form
     * @return int
     */
    protected function saveResult($table, $form)
    {
        return DB::table($table)->insertGetId($form);
    }
}

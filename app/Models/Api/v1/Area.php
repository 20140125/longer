<?php

namespace App\Models\Api\v1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Area extends Base
{
    use HasFactory;
    /**
     * @var string $table
     */
    public $table = 'os_china_area';
    /**
     * @var static $instance
     */
    private static $instance;

    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    /**
     * @return static
     */
    public static function getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * todo:根据条件查询数据
     * @param $where
     * @param string[] $columns
     * @return Model|Builder|object|null
     */
    public function getOne($where, $columns = ['*'])
    {
        return $this->getResult($this->table, $where, $columns);
    }

    /**
     * todo:保存数据
     * @param $where
     * @param $form
     * @return int
     */
    public function updateOne($where, $form)
    {
        return $this->updateResult($this->table, $where, $form);
    }

    /**
     * todo:添加数据
     * @param $form
     * @return int
     */
    public function saveOne($form)
    {
        return $this->saveResult($this->table, $form);
    }

    /**
     * todo:获取列表
     * @param $where
     * @param bool $getAll
     * @param string[] $columns
     * @return Collection
     */
    public function getAreaLists($where, bool $getAll = false, array $columns = ['*'])
    {
        if ($getAll) {
            return DB::table($this->table)->get($columns);
        }
        return DB::table($this->table)->where($where)->get($columns);
    }
}

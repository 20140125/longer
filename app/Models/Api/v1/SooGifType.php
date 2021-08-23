<?php

namespace App\Models\Api\v1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SooGifType extends Base
{
    use HasFactory;

    /**
     * @var string $table
     */
    public $table = 'os_soogif_type';
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
    public function getOne($where, array $columns = ['*'])
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
     * todo:删除记录
     * @param $where
     * @return int
     */
    public function removeOne($where)
    {
        return $this->remove($this->table, $where);
    }

    /**
     * todo:获取数据列表
     * @param $where
     * @param array|string[] $column
     * @return Collection
     */
    public function getLists($where, array $column = ['*'])
    {
        return DB::table($this->table)->where($where)->get($column);
    }
}

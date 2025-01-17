<?php

namespace App\Models\Api\v1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ApiCategory extends Base
{
    use HasFactory;

    /**
     * @var string $table
     */
    public $table = 'os_api_category';
    /**
     * @var static $instance
     */
    private static $instance;

    private function __clone()
    {
        //  Implement __clone() method.
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
     * 根据条件查询数据
     * @param $where
     * @param string[] $columns
     * @return Model|Builder|object|null
     */
    public function getOne($where, array $columns = ['*'])
    {
        return $this->getResult($this->table, $where, $columns);
    }

    /**
     * 保存数据
     * @param $where
     * @param $form
     * @return int
     */
    public function updateOne($where, $form): int
    {
        return $this->updateResult($this->table, $where, $form);
    }

    /**
     * 添加数据
     * @param $form
     * @return int
     */
    public function saveOne($form): int
    {
        return $this->saveResult($this->table, $form);
    }

    /**
     * 删除记录
     * @param $where
     * @return int
     */
    public function removeOne($where): int
    {
        return $this->remove($this->table, $where);
    }

    /**
     * 获取列表记录
     * @param array $where
     * @param string[] $order
     * @param string[] $columns
     * @return Collection
     */
    public function getLists(array $where = [], array $order = ['order' => 'id', 'direction' => 'desc'], array $columns = ['*']): Collection
    {
        return DB::table($this->table)->where($where)->orderBy($order['order'], $order['direction'])->get($columns);
    }
}

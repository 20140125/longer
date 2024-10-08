<?php

namespace App\Models\Api\v1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Auth extends Base
{
    use HasFactory;

    /**
     * @var string $table
     */
    public $table = 'os_auth';
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
    public function getOne($where, $columns = ['*'])
    {
        return $this->getResult($this->table, $where, $columns);
    }

    /**
     * 保存数据
     * @param $where
     * @param $form
     * @return int
     */
    public function updateOne($where, $form)
    {
        return $this->updateResult($this->table, $where, $form);
    }

    /**
     * 添加数据
     * @param $form
     * @return int
     */
    public function saveOne($form)
    {
        return $this->saveResult($this->table, $form);
    }

    /**
     * TODO：权限列表
     * @param array $where
     * @param string[] $column
     * @param array $attr
     * @param string[] $order
     * @return Collection
     */
    public function getLists(array $where = [], array $column = ['*'], array $attr = ['key' => 'id', 'ids' => array()], array $order = ['order' => 'id', 'direction' => 'asc'])
    {
        if (count($attr['ids']) > 0) {
            return DB::table($this->table)->where($where)->whereIn($attr['key'], $attr['ids'])->orderBy($order['order'], $order['direction'])->get($column);
        }
        return DB::table($this->table)->where($where)->orderBy($order['order'], $order['direction'])->get($column);
    }
}

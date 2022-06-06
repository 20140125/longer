<?php

namespace App\Models\Api\v1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Log extends Base
{
    use HasFactory;

    /**
     * @var string $table
     */
    public $table = 'os_system_log';
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
    public static function getInstance(): Log
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
    public function updateOne($where, $form): int
    {
        return $this->updateResult($this->table, $where, $form);
    }

    /**
     * todo:添加数据
     * @param $form
     * @return int
     */
    public function saveOne($form): int
    {
        return $this->saveResult($this->table, $form);
    }

    /**
     * todo:删除记录
     * @param $where
     * @return int
     */
    public function removeOne($where): int
    {
        return $this->remove($this->table, $where);
    }

    /**
     * todo:获取数据列表
     * @param $where
     * @param array|int[] $pagination
     * @param bool $getAll
     * @param array|string[] $order
     * @param array|string[] $column
     * @return Collection | array
     */
    public function getLists($where, array $pagination = ['page' => 1, 'limit' => 10], bool $getAll = false, array $order = ['order' => 'id', 'direction' => 'desc'], array $column = ['*'])
    {
        if ($getAll) {
            return DB::table($this->table)->whereRaw('local is null')->get($column);
        }
        $result['data'] = DB::table($this->table)->where($where)
            ->limit($pagination['limit'])
            ->offset($pagination['limit'] * ($pagination['page'] - 1))
            ->orderBy($order['order'], $order['direction'])
            ->get($column);
        $result['total'] = DB::table($this->table)->where($where)->count();
        return $result;
    }
}

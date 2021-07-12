<?php

namespace App\Models\Api\v1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class TimeLine extends Base
{
    use HasFactory;
    /**
     * @var string $table
     */
    public $table = 'os_timeline';
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
     * todo:获取用户列表
     * @param int[] $pagination
     * @param string[] $order
     * @param false $getAll
     * @param string[] $column
     * @return array|Collection
     */
    public function getLists($pagination = ['page' => 1, 'limit' => 10], $order = ['order' => 'id', 'direction' => 'asc'], $getAll = false, $column = ['*'])
    {
        if ($getAll) {
            return DB::table($this->table)->get($column);
        }
        $result['data'] = DB::table($this->table)
            ->limit($pagination['limit'])
            ->offset($pagination['limit'] * ($pagination['page'] - 1))
            ->orderBy($order['order'],$order['direction'])
            ->get($column);
        $result['total'] = DB::table($this->table)->count();
        return $result;
    }
}

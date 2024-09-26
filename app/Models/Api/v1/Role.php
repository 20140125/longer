<?php

namespace App\Models\Api\v1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Role extends Base
{
    use HasFactory;

    /**
     * @var string $table
     */
    public $table = 'os_role';
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
     * 获取角色列表
     * @param $where
     * @param int[] $pagination
     * @param string[] $order
     * @param bool $getAll
     * @param string[] $columns
     * @return Collection | array
     */
    public function getLists($where, array $pagination = ['page' => 1, 'limit' => 10], array $order = ['order' => 'id', 'direction' => 'desc'], bool $getAll = false, array $columns = ['*'])
    {
        if ($getAll) {
            return DB::table($this->table)->where($where)->get($columns);
        }
        $result['data'] = DB::table($this->table)->where($where)
            ->limit($pagination['limit'])
            ->offset($pagination['limit'] * ($pagination['page'] - 1))
            ->orderBy($order['order'], $order['direction'])
            ->get($columns);
        $result['total'] = DB::table($this->table)->where($where)->count();
        return $result;
    }
}

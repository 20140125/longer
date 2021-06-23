<?php

namespace App\Models\Api\v1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Oauth extends Base
{
    use HasFactory;
    /**
     * @var string $table
     */
    public $table = 'os_oauth';
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
     * @param $user
     * @param array|int[] $pagination
     * @param array|string[] $order
     * @param bool $getAll
     * @param array|string[] $column
     * @return array|Collection
     */
    public function getLists($user, array $pagination = ['page' => 1, 'limit' => 10], array $order = ['order' => 'id', 'direction' => 'asc'], bool $getAll = false, array $column = ['*'])
    {
        if ($getAll) {
            return DB::table($this->table)->get($column);
        }
        $where = [];
        if (!empty($user) && !in_array($user->role_id, [1])) {
            $where[] = ['os_oauth.id', $user->uid];
        }
        $result['data'] = DB::table($this->table)->join('os_role', $this->table.'.role_id', '=', 'os_role.id')
            ->limit($pagination['limit'])->offset($pagination['limit'] * ($pagination['page'] - 1))
            ->orderBy($order['order'],$order['direction'])
            ->where($where)
            ->get(['os_oauth.*', 'os_role.id as role_id']);
        $result['total'] = DB::table($this->table)->where($where)->count();
        return $result;
    }
}

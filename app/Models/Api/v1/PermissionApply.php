<?php

namespace App\Models\Api\v1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class PermissionApply extends Base
{
    use HasFactory;

    /**
     * @var string $table
     */
    public $table = 'os_permission_apply';
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
    public static function getInstance(): PermissionApply
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
     * todo:获取申请权限列表
     * @param $user
     * @param array $where
     * @param int[] $pagination
     * @param string[] $order
     * @param string[] $columns
     * @return array
     */
    public function getLists($user, array $where = [], array $pagination = ['page' => 1, 'limit' => 10], array $order = ['order' => 'id', 'direction' => 'desc'], array $columns = ['*']): array
    {
        if (!empty($user) && $user->role_id != 1) {
            $where[] = ['user_id', empty($user->oauth_type) ? $user->id : $user->uid];
        }
        $result['data'] = DB::table($this->table)->limit($pagination['limit'])
            ->offset($pagination['limit'] * ($pagination['page'] - 1))
            ->orderBy($order['order'], $order['direction'])
            ->where($where)
            ->get($columns);
        $result['total'] = DB::table($this->table)->where($where)->count();
        return $result;
    }
}

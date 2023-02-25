<?php

namespace App\Models\Api\v1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PermissionApplyLog extends Base
{
    use HasFactory;

    /**
     * @var string $table
     */
    public $table = 'os_permission_apply_log';
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
    public static function getInstance(): PermissionApplyLog
    {
        if (!self::$instance instanceof self) {
            self::$instance = new static();
        }
        return self::$instance;
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
     * 获取申请权限列表
     * @param array $where
     * @param string[] $order
     * @param string[] $columns
     * @return Collection
     */
    public function getLists(array $where = [], array $order = ['order' => 'id', 'direction' => 'desc'], array $columns = ['*']): Collection
    {
        return DB::table($this->table)->orderBy($order['order'], $order['direction'])->where($where)->limit(15)->get($columns);
    }
}

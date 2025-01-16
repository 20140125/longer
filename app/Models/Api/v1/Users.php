<?php

namespace App\Models\Api\v1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Users extends Base
{
    use HasFactory;

    /**
     * @var string $table
     */
    public $table = 'os_users';
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
     * @param $oauth
     * @return int
     */
    public function saveOne($oauth): int
    {
        $salt = getRoundNum(8, 'all');
        $userArray = [
            'username'       => $oauth->username,
            'avatar_url'     => $oauth->avatar_url,
            'remember_token' => $oauth->remember_token,
            'email'          => $oauth->email ?? '',
            'salt'           => $salt,
            'password'       => md5(md5('123456789') . $salt),
            'role_id'        => $oauth->role_id,
            'ip_address'     => request()->ip(),
            'created_at'     => time(),
            'updated_at'     => time(),
            'status'         => $oauth->status,
            'phone_number'   => '',
            'uuid'           => ''
        ];
        return $this->saveResult($this->table, $userArray);
    }

    /**
     * 获取用户列表
     * @param $user
     * @param array|int[] $pagination
     * @param array|string[] $order
     * @param bool $getAll
     * @param array|string[] $column
     * @param array $form
     * @return array|Collection
     */
    public function getLists($user, array $pagination = ['page' => 1, 'limit' => 10], array $order = ['order' => 'id', 'direction' => 'asc'], bool $getAll = false, array $column = ['*'], array $form = [])
    {
        if ($getAll) {
            return DB::table($this->table)->get($column);
        }
        $where = [];
        if (!empty($user) && $user->role_id != 1) {
            $where[] = ['id', $user->id];
        }
        if (!empty($form['username'])) {
            $where[] = ['username', $form['username']];
        }
        $result['data'] = DB::table($this->table)
            ->limit($pagination['limit'])->offset($pagination['limit'] * ($pagination['page'] - 1))
            ->orderBy($order['order'], $order['direction'])
            ->where($where)
            ->get($column);
        $result['total'] = DB::table($this->table)->where($where)->count();
        return $result;
    }
}

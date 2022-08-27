<?php

namespace App\Models\Api\v1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Emotion extends Base
{
    use HasFactory;

    /**
     * @var string $table
     */
    public $table = 'os_emotion';
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
    public static function getInstance(): Emotion
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
     * todo:获取用户列表
     * @param array $pagination
     * @param string $type
     * @param array $order
     * @param bool $getAll
     * @param array $column
     * @return array
     */
    public function getLists(array $pagination = ['page' => 1, 'limit' => 10], string $type = '', array $order = ['order' => 'id', 'direction' => 'asc'], bool $getAll = false, array $column = ['*']): array
    {
        if ($getAll) {
            $result['data'] = DB::table($this->table)->where(['type' => $type])->get($column);
            \Illuminate\Support\Facades\Log::error(json_encode($result));
            return $result;
        }
        $result['data'] = DB::table($this->table)
            ->limit($pagination['limit'])
            ->where(['type' => $type])
            ->offset($pagination['limit'] * ($pagination['page'] - 1))
            ->orderBy($order['order'], $order['direction'])
            ->get($column);
        $result['total'] = DB::table($this->table)->where(['type' => $type])->count();
        \Illuminate\Support\Facades\Log::error(json_encode($result));
        return $result;
    }
}

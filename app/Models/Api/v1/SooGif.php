<?php

namespace App\Models\Api\v1;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class SooGif extends Base
{
    use HasFactory;

    /**
     * @var static $instance
     */
    private static $instance;
    /**
     * @var string $table
     */
    public $table = 'os_soogif';

    /**
     * @return static
     */
    public static function getInstance(): SooGif
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
     * @param array|string[] $order
     * @param array|string[] $column
     * @return array
     */
    public function getLists($where, array $pagination = ['page' => 1, 'limit' => 10], array $order = ['order' => 'id', 'direction' => 'desc'], array $column = ['*']): array
    {
        if ($order['order'] === 'rand') {
            $offset = $pagination['limit'] * ($pagination['page'] - 1);
            $result['data'] = DB::select("SELECT t1.* FROM `os_soogif` AS t1 JOIN (SELECT ROUND(RAND() * ((SELECT MAX(id) FROM `os_soogif`)-(SELECT MIN(id) FROM `os_soogif`))+(SELECT MIN(id) FROM `os_soogif`)) AS id) AS t2 WHERE t1.id >= t2.id ORDER BY t1.id LIMIT {$offset}, {$pagination['limit']}");
            $result['total'] = DB::table($this->table)->where($where)->count();
            return $result;
        }
        $result['data'] = DB::table($this->table)
            ->where($where)
            ->limit($pagination['limit'])
            ->offset($pagination['limit'] * ($pagination['page'] - 1))
            ->orderBy($order['order'], $order['direction'])
            ->get($column);
        $result['total'] = DB::table($this->table)->where($where)->count();
        return $result;
    }

    private function __clone()
    {
        // TODO: Implement __clone() method.
    }
}

<?php

namespace App\Http\Controllers\Service\v1;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;

class TimeLineService extends BaseService
{
    /**
     * @var static $instance
     */
    private static $instance;
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
     * todo:获取用户
     * @param $where
     * @param string[] $columns
     * @return Model|Builder|object|null
     */
    public function getTimeLine($where, $columns = ['*'])
    {
        return $this->timeLineModel->getOne($where, $columns);
    }

    /**
     * todo:获取列表
     * @param int[] $pagination
     * @param string[] $order
     * @param false $getAll
     * @param string[] $column
     * @return array|Collection
     */
    public function getLists($pagination = ['page' => 1, 'limit' => 10], $order = ['order' => 'id', 'direction' => 'asc'], $getAll = false, $column = ['*'])
    {
        $this->return['lists'] = $this->timeLineModel->getLists($pagination, $order, $getAll, $column);
        return $this->return;
    }
}

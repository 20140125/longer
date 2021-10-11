<?php

namespace App\Http\Controllers\Service\v1;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class EmotionService extends BaseService
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
     * todo:获取列表
     * @param int[] $pagination
     * @param string $where
     * @param string[] $order
     * @param false $getAll
     * @param string[] $column
     * @return array
     */
    public function getLists(array $pagination = ['page' => 1, 'limit' => 10], string $where = '', array $order = ['order' => 'id', 'direction' => 'asc'], bool $getAll = false, array $column = ['*'])
    {
        $this->return['lists'] = Cache::get('emotion_'.$where);
        if (empty($this->return['lists'])) {
            $this->return['lists'] = $this->emotionModel->getLists($pagination, $where, $order, $getAll, $column);
            Cache::forever('emotion_'.$where, $this->return['lists']);
        }
        return $this->return;
    }
}

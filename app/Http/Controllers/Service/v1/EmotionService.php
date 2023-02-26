<?php

namespace App\Http\Controllers\Service\v1;

class EmotionService extends BaseService
{
    /**
     * @var static $instance
     */
    private static $instance;

    /**
     * @return static
     */
    public static function getInstance(): EmotionService
    {
        if (!self::$instance instanceof self) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * 获取列表
     * @param int[] $pagination
     * @param string $where
     * @param string[] $order
     * @param false $getAll
     * @param string[] $column
     * @return array
     */
    public function getLists(array $pagination = ['page' => 1, 'limit' => 10], string $where = '', array $order = ['order' => 'id', 'direction' => 'asc'], bool $getAll = false, array $column = ['*']): array
    {
        $this->return['lists'] = $this->emotionModel->getLists($pagination, $where, $order, $getAll, $column);
        return $this->return;
    }
}

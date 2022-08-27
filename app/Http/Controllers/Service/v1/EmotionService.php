<?php

namespace App\Http\Controllers\Service\v1;

use Carbon\Carbon;
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
    public static function getInstance(): EmotionService
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
    public function getLists(array $pagination = ['page' => 1, 'limit' => 10], string $where = '', array $order = ['order' => 'id', 'direction' => 'asc'], bool $getAll = false, array $column = ['*']): array
    {
        $value = $this->redisClient->getValue('emotion_type_'.$where);
        $this->return['lists'] = json_decode(mb_substr($value, 0, strripos($value, '"') + 3), true);
        if (empty($this->return['lists'])) {
            $this->return['lists'] = $this->emotionModel->getLists($pagination, $where, $order, $getAll, $column);
            if ($getAll) {
                $this->return['lists'] = $this->redisClient->setValue('emotion_type_'.$where, json_encode($this->return['lists']), ['EX'=> config('app.app_refresh_login_time')]);
            }
        }
        return $this->return;
    }
}

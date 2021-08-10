<?php

namespace App\Http\Controllers\Service\v1;

use App\Models\Api\v1\SooGif;
use App\Models\Api\v1\SooGifType;

class ImageService extends BaseService
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
     * todo:获取图片列表
     * @param $form
     * @param int[] $pagination
     * @param string[] $order
     * @param string[] $columns
     * @return array
     */
    public function getImageLists($form, array $pagination = ['page' => 1, 'limit' => 10], array $order = ['order' => 'id', 'direction' => 'desc'], array $columns = ['*'])
    {
        $where = [];
        if (!empty($form['id'])) {
            $where[] = ['type', $form['id']];
        }
        if (!empty($form['name'])) {
            $where[] = ['type',  SooGifType::getInstance()->getOne([['name', 'like', "%{$form['name']}%"]], ['id'])->id];
        }
        $this->return['lists'] = SooGif::getInstance()->getLists($where, $pagination, $order, $columns);
        return $this->return;
    }
}

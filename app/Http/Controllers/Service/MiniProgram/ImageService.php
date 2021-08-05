<?php

namespace App\Http\Controllers\Service\MiniProgram;

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
     * todo:获取图片类型
     * @param $form
     * @return array
     */
    public function getImageTypeLists($form)
    {
        $systemConfig = json_decode($this->systemConfig, true);
        $where = [];
        foreach ($systemConfig as $item) {
            if ($item['name'] === 'NOT_EQUAL_TO_TYPE') {
                $where[] = ['id', '<>', (int)$item['value']];
            }
        }
        $where[] = ['pid', '=', $form['parent_id'] ?? 0];
        $this->return['lists'] = $this->sooGifTypeModel->getLists($where);
        return $this->return;
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
            $where[] = ['type',  $this->sooGifTypeModel->getOne(['name' => $form['name']], ['id'])->id];
        }
        $this->return['lists'] = $this->sooGifModel->getLists($where, $pagination, $order, $columns);
        return $this->return;
    }
}

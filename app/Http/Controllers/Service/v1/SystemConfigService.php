<?php

namespace App\Http\Controllers\Service\v1;

use App\Http\Controllers\Utils\Code;

class SystemConfigService extends BaseService
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
     * todo:获取系统配置
     * @param array|int[] $pagination
     * @param array|string[] $order
     * @param array|string[] $columns
     */
    public function getSystemConfigLists(array $pagination = ['page' => 1, 'limit' => 10], array $order = ['order' => 'id', 'direction' => 'desc'], array $columns = ['*'])
    {
        $this->return['lists'] = $this->systemConfigModel->getLists($pagination, $order, $columns);
        $intFields = ['status','id','pid'];
        foreach ($this->return['lists']['data'] as &$item) {
            $item->created_at = date('Y-m-d H:i:s', $item->created_at);
            $item->updated_at = date('Y-m-d H:i:s', $item->updated_at);
            $item->children = $item->children ? json_decode($item->children, true) : [];
            foreach ($intFields as $int) {
                foreach ($item->children as &$child) {
                    $child[$int] = (int)$child[$int];
                }
            }
        }
        return $this->return;
    }

    /**
     * todo:获取系统配置
     * @param $form
     * @return array
     */
    public function getConfig($form)
    {
        $this->return['lists'] = $this->systemConfigModel->getOne(['name' => $form['name']], ['children']);
        $this->return['lists']->children = json_decode($this->return['lists']->children, true);
        return $this->return;
    }

    /**
     * @param $form
     * @return array
     */
    public function saveSystemConfig($form)
    {
        foreach ($form['children'] as &$item) {
            $item['created_at'] = date('Y-m-d H:i:s', time());
            $item['updated_at'] = date('Y-m-d H:i:s', time());
        }
        $form['created_at'] = time();
        $form['updated_at'] = time();
        $form['children'] = json_encode($form['children'], JSON_UNESCAPED_UNICODE);
        $result = $this->systemConfigModel->saveOne($form);
        /* 更新PID */
        $form['children'] = json_decode($form['children'], true);
        foreach ($form['children'] as $key => &$item) {
            $item['pid'] = $result;
            $item['id'] = $item['id'] ?? $result * 1000 + $key + 1;
        }
        $result = $this->systemConfigModel->updateOne(['id' => $result], $form);
        if (empty($result)) {
            $this->return['code'] = Code::ERROR;
            $this->return['message'] = 'save system config failed';
            return $this->return;
        }
        $this->return['lists'] = $form;
        return $this->return;
    }

    /**
     * todo:更新配置
     * @param $form
     * @return array
     */
    public function updateSystemConfig($form)
    {
        foreach ($form['children'] as $key => &$item) {
            $item['updated_at'] = date('Y-m-d H:i:s', time());
            $item['id'] = $item['id'] ?? $form['id'] * 1000 + $key + 1;
        }
        $form['children'] = json_encode($form['children'], JSON_UNESCAPED_UNICODE);
        $form['created_at'] = strtotime($form['created_at']);
        $form['updated_at'] = time();
        $result = $this->systemConfigModel->updateOne(['id' => $form['id']], $form);
        if (empty($result)) {
            $this->return['code'] = Code::ERROR;
            $this->return['message'] = 'update system config failed';
            return $this->return;
        }
        $this->return['lists'] = $form;
        return $this->return;
    }
}

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
        $result = $this->systemConfigModel->saveOne($form);
        if (empty($result)) {
            $this->return['code'] = Code::ERROR;
            $this->return['message'] = 'save system config failed';
            return $this->return;
        }
        $this->return['lists'] = $form;
        return $this->return;
    }
}

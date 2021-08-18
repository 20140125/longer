<?php

namespace App\Http\Controllers\Service\v1;

use App\Jobs\SyncImageListsProcess;
use App\Jobs\SyncImageSizeProcess;
use App\Jobs\SyncImageTypeProcess;

class SpiderService extends BaseService
{
    /**
     * todo:获取爬虫配置
     * @return array
     */
    public function getSpiderConfig()
    {
        $result = $this->systemConfigModel->getOne(['name' => 'SpiderConfig'], ['children'])->children;
        $this->return['lists'] = json_decode($result ?? (Object)[], true);
        foreach ($this->return['lists'] as &$item) {
            $item->value = json_decode($item->value, true);
        }
        return $this->return;
    }
    /**
     * todo:同步图片类型
     * @param $form
     * @return array
     */
    public function syncImageType($form)
    {
        dispatch(new SyncImageTypeProcess($form))->onQueue('spider');
        return $this->return;
    }
    /**
     * todo:同步图片
     * @param $form
     * @return array
     */
    public function syncImageLists($form)
    {
        dispatch(new SyncImageListsProcess($form))->onQueue('spider');
        return $this->return;
    }
    /**
     * todo:同步图片
     * @param $form
     * @return array
     */
    public function syncImageSize($form)
    {
        dispatch(new SyncImageSizeProcess($form))->onQueue('spider');
        return $this->return;
    }
}

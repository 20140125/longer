<?php

namespace App\Http\Controllers\Service\v1;

use App\Jobs\SyncCityWeatherProcess;
use App\Jobs\SyncImageListsForTagsProcess;
use App\Jobs\SyncImageListsProcess;
use App\Jobs\SyncImageSizeProcess;
use App\Jobs\SyncImageTypeProcess;
use App\Jobs\SyncOauthProcess;
use App\Jobs\SyncSooGifImageProcess;

class SpiderService extends BaseService
{
    /**
     * todo:获取爬虫配置
     * @return array
     */
    public function getSpiderConfig()
    {
        $result = $this->systemConfigModel->getOne(['name' => 'SpiderConfig'], ['children'])->children;
        $this->return['lists'] = json_decode($result ?? (object)[], true);
        foreach ($this->return['lists'] as &$item) {
            $item['value'] = json_decode($item['value'], true);
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
     * todo:同步图片列表
     * @param $form
     * @return array
     */
    public function syncImageLists($form)
    {
        dispatch(new SyncImageListsProcess($form))->onQueue('spider');
        return $this->return;
    }

    /**
     * todo:同步图片大小
     * @param $form
     * @return array
     */
    public function syncImageSize($form)
    {
        dispatch(new SyncImageSizeProcess($form))->onQueue('spider');
        return $this->return;
    }

    /**
     * todo:同步授权用户
     * @param $form
     * @return array
     */
    public function syncImageListsForTags($form)
    {
        dispatch(new SyncImageListsForTagsProcess($form))->onQueue('spider');
        return $this->return;
    }
    /**
     * todo:同步动态图片
     * @param $form
     * @return array
     */
    public function syncSpiderImageSoogif($form)
    {
        dispatch(new SyncSooGifImageProcess($form))->onQueue('spider');
        return $this->return;
    }

    /**
     * todo:同步授权用户
     * @param $form
     * @return array
     */
    public function syncOauth($form)
    {
        dispatch(new SyncOauthProcess($form))->onQueue('users');
        return $this->return;
    }
}

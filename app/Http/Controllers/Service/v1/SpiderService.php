<?php

namespace App\Http\Controllers\Service\v1;

use App\Jobs\SyncImageListsForTagsProcess;
use App\Jobs\SyncImageListsProcess;
use App\Jobs\SyncImageSizeProcess;
use App\Jobs\SyncImageTypeProcess;
use App\Jobs\SyncOauthProcess;
use App\Jobs\SyncSooGifImageProcess;

class SpiderService extends BaseService
{
    /**
     * @var static $instance
     */
    private static $instance;

    /**
     * @return static
     */
    public static function getInstance(): SpiderService
    {
        if (!self::$instance instanceof self) {
            self::$instance = new static();
        }
        return self::$instance;
    }
    /**
     * 获取爬虫配置
     * @return array
     */
    public function getSpiderConfig(): array
    {
        $result = $this->systemConfigModel->getOne(['name' => 'SpiderConfig'], ['children'])->children;
        $this->return['lists'] = json_decode($result ?? (object)[], true);
        foreach ($this->return['lists'] as &$item) {
            $item['value'] = json_decode($item['value'], true);
        }
        return $this->return;
    }

    /**
     * 同步图片类型
     * @param $form
     * @return array
     */
    public function syncImageType($form): array
    {
        dispatch(new SyncImageTypeProcess($form))->onQueue('spider');
        return $this->return;
    }

    /**
     * 同步图片列表
     * @param $form
     * @return array
     */
    public function syncImageLists($form): array
    {
        dispatch(new SyncImageListsProcess($form))->onQueue('spider');
        return $this->return;
    }

    /**
     * 同步图片大小
     * @param $form
     * @return array
     */
    public function syncImageSize($form): array
    {
        dispatch(new SyncImageSizeProcess($form))->onQueue('spider');
        return $this->return;
    }

    /**
     * 同步授权用户
     * @param $form
     * @return array
     */
    public function syncImageListsForTags($form): array
    {
        dispatch(new SyncImageListsForTagsProcess($form))->onQueue('spider');
        return $this->return;
    }
    /**
     * 同步动态图片
     * @param $form
     * @return array
     */
    public function syncSpiderImageSoogif($form): array
    {
        dispatch(new SyncSooGifImageProcess($form))->onQueue('spider');
        return $this->return;
    }

    /**
     * 同步授权用户
     * @param $form
     * @return array
     */
    public function syncOauth($form): array
    {
        dispatch(new SyncOauthProcess($form))->onQueue('users');
        return $this->return;
    }
}

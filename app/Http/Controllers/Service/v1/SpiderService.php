<?php

namespace App\Http\Controllers\Service\v1;

use App\Jobs\SyncImageListsProcess;
use App\Jobs\SyncImageSizeProcess;
use App\Jobs\SyncImageTypeProcess;

class SpiderService extends BaseService
{
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

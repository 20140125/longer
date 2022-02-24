<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SpiderController extends BaseController
{
    /**
     * todo:获取系统配置
     * @return JsonResponse
     */
    public function getSpiderConfig(Request $request)
    {
        validatePost($request->get('item'));
        $result = $this->spiderService->getSpiderConfig();
        return ajaxReturn($result);
    }

    /**
     * todo:执行脚本
     * @param Request $request
     * @return JsonResponse|void
     */
    public function runningSpider(Request $request)
    {
        validatePost($request->get('item'), $this->post, ['keywords' => 'required|string', 'method' => 'required|string']);
        $_user = $request->get('unauthorized');
        switch ($this->post['method']) {
            case 'syncImageType':
                return $this->syncImageType($_user);
            case 'syncImageLists':
                return $this->syncImageLists($_user);
            case 'syncImageSize':
                return $this->syncImageSize($_user);
            case 'syncOauth':
                return $this->syncOauth($_user);
            case 'syncImageListsForTags':
                return $this->syncImageListsForTags($_user);
            case 'syncSpiderImageSoogif':
                return $this->syncSpiderImageSoogif($_user);
        }
    }

    /**
     * todo:获取图片列表
     * @param $_user
     * @return JsonResponse
     */
    protected function syncImageType($_user)
    {
        $result = $this->spiderService->syncImageType(['keywords' => $this->post['keywords'], 'uuid' => $_user->uuid]);
        return ajaxReturn($result);
    }

    /**
     * todo:获取图片列表
     * @param $_user
     * @return JsonResponse
     */
    protected function syncImageLists($_user)
    {
        $result = $this->spiderService->syncImageLists(['keywords' => $this->post['keywords'], 'uuid' => $_user->uuid]);
        return ajaxReturn($result);
    }

    /**
     * todo:同步图片宽高
     * @param $_user
     * @return JsonResponse
     */
    protected function syncImageSize($_user)
    {
        $result = $this->spiderService->syncImageSize(['keywords' => $this->post['keywords'], 'uuid' => $_user->uuid]);
        return ajaxReturn($result);
    }

    /**
     * todo:同步授权用户信息
     * @param $_user
     * @return JsonResponse
     */
    protected function syncOauth($_user)
    {
        $result = $this->spiderService->syncOauth(['keywords' => $this->post['keywords'], 'uuid' => $_user->uuid]);
        return ajaxReturn($result);
    }

    /**
     * todo:同步授权用户信息
     * @param $_user
     * @return JsonResponse
     */
    protected function syncImageListsForTags($_user)
    {
        $result = $this->spiderService->syncImageListsForTags(['keywords' => $this->post['keywords'], 'uuid' => $_user->uuid]);
        return ajaxReturn($result);
    }
    /**
     * todo:同步授权用户信息
     * @param $_user
     * @return JsonResponse
     */
    protected function syncSpiderImageSoogif($_user)
    {
        $result = $this->spiderService->syncSpiderImageSoogif(['keywords' => $this->post['keywords'], 'uuid' => $_user->uuid]);
        return ajaxReturn($result);
    }
}

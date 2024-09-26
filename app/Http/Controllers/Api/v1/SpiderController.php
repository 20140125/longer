<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SpiderController extends BaseController
{
    /**
     * 获取系统配置
     * @return JsonResponse
     */
    public function getSpiderConfig()
    {
        $result = $this->spiderService->getSpiderConfig();
        return ajaxReturn($result);
    }

    /**
     * 执行脚本
     * @param Request $request
     * @return JsonResponse|void
     */
    public function runningSpider(Request $request)
    {
        validatePost($this->post, ['keywords' => 'required|string', 'method' => 'required|string']);
        $user = $request->get('unauthorized');
        switch ($this->post['method']) {
            case 'syncImageType':
                return $this->syncImageType($user);
            case 'syncImageLists':
                return $this->syncImageLists($user);
            case 'syncImageSize':
                return $this->syncImageSize($user);
            case 'syncOauth':
                return $this->syncOauth($user);
            case 'syncImageListsForTags':
                return $this->syncImageListsForTags($user);
            case 'syncSpiderImageSoogif':
                return $this->syncSpiderImageSoogif($user);
        }
    }

    /**
     * 获取图片列表
     * @param $user
     * @return JsonResponse
     */
    protected function syncImageType($user)
    {
        $result = $this->spiderService->syncImageType(['keywords' => $this->post['keywords'], 'uuid' => $user->uuid]);
        return ajaxReturn($result);
    }

    /**
     * 获取图片列表
     * @param $user
     * @return JsonResponse
     */
    protected function syncImageLists($user)
    {
        $result = $this->spiderService->syncImageLists(['keywords' => $this->post['keywords'], 'uuid' => $user->uuid]);
        return ajaxReturn($result);
    }

    /**
     * 同步图片宽高
     * @param $user
     * @return JsonResponse
     */
    protected function syncImageSize($user)
    {
        $result = $this->spiderService->syncImageSize(['keywords' => $this->post['keywords'], 'uuid' => $user->uuid]);
        return ajaxReturn($result);
    }

    /**
     * 同步授权用户信息
     * @param $user
     * @return JsonResponse
     */
    protected function syncOauth($user)
    {
        $result = $this->spiderService->syncOauth(['keywords' => $this->post['keywords'], 'uuid' => $user->uuid]);
        return ajaxReturn($result);
    }

    /**
     * 同步授权用户信息
     * @param $user
     * @return JsonResponse
     */
    protected function syncImageListsForTags($user)
    {
        $result = $this->spiderService->syncImageListsForTags(['keywords' => $this->post['keywords'], 'uuid' => $user->uuid]);
        return ajaxReturn($result);
    }
    /**
     * 同步授权用户信息
     * @param $user
     * @return JsonResponse
     */
    protected function syncSpiderImageSoogif($user)
    {
        $result = $this->spiderService->syncSpiderImageSoogif(['keywords' => $this->post['keywords'], 'uuid' => $user->uuid]);
        return ajaxReturn($result);
    }
}

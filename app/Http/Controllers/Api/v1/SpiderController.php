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
    public function getSpiderConfig()
    {
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
        validatePost($this->post, ['keywords' => 'required|string', 'method' => 'required|string']);
        switch ($this->post['method']) {
            case 'syncImageType':
                return $this->syncImageType($request);
            case 'syncImageLists':
                return $this->syncImageLists($request);
            case 'syncImageSize':
                return $this->syncImageSize($request);
        }
    }

    /**
     * todo:获取图片列表
     * @return JsonResponse
     */
    protected function syncImageType(Request $request)
    {
        $_user = $request->get('unauthorized');
        $result = $this->spiderService->syncImageType(['keywords' => $this->post['keywords'], 'uuid' => $_user->uuid]);
        return ajaxReturn($result);
    }

    /**
     * todo:获取图片列表
     * @return JsonResponse
     */
    protected function syncImageLists(Request $request)
    {
        $_user = $request->get('unauthorized');
        $result = $this->spiderService->syncImageLists(['keywords' => $this->post['keywords'], 'uuid' => $_user->uuid]);
        return ajaxReturn($result);
    }

    /**
     * todo:同步图片宽高
     * @param Request $request
     * @return JsonResponse
     */
    protected function syncImageSize(Request $request)
    {
        $_user = $request->get('unauthorized');
        $result = $this->spiderService->syncImageSize(['keywords' => $this->post['keywords'], 'uuid' => $_user->uuid]);
        return ajaxReturn($result);
    }
    /**
     * todo:同步授权用户信息
     * @param Request $request
     * @return JsonResponse
     */
    protected function syncOauth(Request $request)
    {
        $_user = $request->get('unauthorized');
        $result = $this->spiderService->syncImageSize(['keywords' => $this->post['keywords'], 'uuid' => $_user->uuid]);
        return ajaxReturn($result);
    }
}

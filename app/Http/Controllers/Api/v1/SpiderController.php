<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SpiderController extends BaseController
{
    /**
     * todo:获取图片列表
     * @return JsonResponse
     */
    public function syncImageType(Request $request)
    {
        validatePost($this->post, ['keywords' => 'required|string']);
        $_user = $request->get('unauthorized');
        $result = $this->spiderService->syncImageType(['keywords' => $this->post['keywords'], 'uuid' => $_user->uuid]);
        return ajaxReturn($result);
    }

    /**
     * todo:获取图片列表
     * @return JsonResponse
     */
    public function syncImageLists(Request $request)
    {
        validatePost($this->post, ['keywords' => 'required|string']);
        $_user = $request->get('unauthorized');
        $result = $this->spiderService->syncImageLists(['keywords' => $this->post['keywords'], 'uuid' => $_user->uuid]);
        return ajaxReturn($result);
    }
}

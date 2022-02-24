<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PushController extends BaseController
{
    /**
     * todo:获取站内消息
     * @param Request $request
     * @return JsonResponse
     */
    public function getPushLists(Request $request)
    {
        validatePost($request->get('item'), $this->post, ['page' => 'required|integer', 'limit' => 'required|integer']);
        $_user = $request->get('unauthorized');
        $result = $this->pushService->getPushLists($this->post, $_user, ['page' => $this->post['page'], 'limit' => $this->post['limit']]);
        return ajaxReturn($result);
    }

    /**
     * todo:发布站内通知
     * @return JsonResponse
     */
    public function savePush(Request $request)
    {
        validatePost($request->get('item'), $this->post, ['info' => 'required|string', 'username' => 'required|string', 'status' => 'required|integer|in:1,2', 'uuid' => 'required|string']);
        $result = $this->pushService->savePush($this->post);
        return ajaxReturn($result);
    }

    /**
     * todo:更新站内通知
     * @return JsonResponse
     */
    public function updatePush(Request $request)
    {
        validatePost($request->get('item'), $this->post, ['id' => 'required|integer', 'info' => 'required|string', 'username' => 'required|string', 'status' => 'required|integer|in:1,2', 'uuid' => 'required|string']);
        $result = $this->pushService->updatePush($this->post);
        return ajaxReturn($result);
    }

    /**
     * todo:删除站内通知
     * @return JsonResponse
     */
    public function removePush(Request $request)
    {
        validatePost($request->get('item'), $this->post, ['id' => 'required|integer']);
        $result = $this->pushService->removePush($this->post);
        return ajaxReturn($result);
    }
}

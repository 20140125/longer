<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogController extends BaseController
{
    /**
     * 获取日志列表
     * @param Request $request
     * @return JsonResponse
     */
    public function getLogLists(Request $request): JsonResponse
    {
        validatePost($request->get('item'), $this->post, ['page' => 'required|integer', 'limit' => 'required|integer']);
        $_user = $request->get('unauthorized');
        $result = $this->logService->getLists($_user, ['page' => $this->post['page'], 'limit' => $this->post['limit']]);
        return ajaxReturn($result);
    }

    /**
     * 获取日志信息
     * @param Request $request
     * @return JsonResponse
     */
    public function getLog(Request $request): JsonResponse
    {
        validatePost($request->get('item'), $this->post, ['id' => 'required|integer']);
        $_user = $request->get('unauthorized');
        $result = $this->logService->getLog($_user, ['id' => $this->post['id']], ['log']);
        return ajaxReturn($result);
    }

    /**
     * 日志删除
     * @param Request $request
     * @return JsonResponse
     */
    public function removeLog(Request $request): JsonResponse
    {
        validatePost($request->get('item'), $this->post, ['id' => 'required|integer']);
        $_user = $request->get('unauthorized');
        $result = $this->logService->removeLog($_user, $this->post);
        return ajaxReturn($result);
    }
}

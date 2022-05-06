<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LogController extends BaseController
{
    /**
     * todo:获取日志列表
     * @param Request $request
     * @return JsonResponse
     */
    public function getLogLists(Request $request)
    {
        validatePost($request->get('item'), $this->post, ['page' => 'required|integer', 'limit' => 'required|integer']);
        $_user = $request->get('unauthorized');
        $result = $this->logService->getLists($_user, ['page' => $this->post['page'], 'limit' => $this->post['limit']]);
        return ajaxReturn($result);
    }

    /**
     * todo:获取日志信息
     * @param Request $request
     * @return JsonResponse
     */
    public function getLog(Request $request)
    {
        validatePost($request->get('item'), $this->post, ['id' => 'required|integer']);
        $_user = $request->get('unauthorized');
        $result = $this->logService->getLog($_user, ['id' => $this->post['id']], ['log']);
        return ajaxReturn($result);
    }

    /**
     * todo:日志删除
     * @param Request $request
     * @return JsonResponse
     */
    public function removeLog(Request $request)
    {
        validatePost($request->get('item'), $this->post, ['id' => 'required|integer']);
        $_user = $request->get('unauthorized');
        $result = $this->logService->removeLog($_user, $this->post);
        return ajaxReturn($result);
    }
}

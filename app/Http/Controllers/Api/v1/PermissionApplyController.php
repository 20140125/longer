<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PermissionApplyController extends BaseController
{
    /**
     * 获取申请权限列表
     * @param Request $request
     * @return JsonResponse
     */
    public function getPermissionApplyLists(Request $request)
    {
        validatePost($this->post, ['page' => 'required|integer', 'limit' => 'required|integer']);
        $_user = $request->get('unauthorized');
        $result = $this->permissionApplyService->getPermissionApplyLists($_user, [], ['page' => $this->post['page'], 'limit' => $this->post['limit']]);
        return ajaxReturn($result);
    }

    /**
     * 获取用户权限
     * @return JsonResponse
     */
    public function getUserAuth()
    {
        validatePost($this->post, ['user_id' => 'required|integer']);
        $result = $this->permissionApplyService->getUserAuth(is_numeric($this->post['user_id']) ? $this->post['user_id'] : decrypt($this->post['user_id']));
        return ajaxReturn($result);
    }

    /**
     * 添加申请权限
     * @return JsonResponse
     */
    public function savePermissionApply()
    {
        validatePost($this->post, [
            'user_id' => 'required|integer',
            'expires' => 'required|date|before:' . date('Y-m-d H:i:s', strtotime('+1 year')) . '|after:' . date('Y-m-d H:i:s'),
            'desc'    => 'required|string',
            'href'    => 'required|string'
        ]);
        $result = $this->permissionApplyService->savePermissionApply($this->post);
        return ajaxReturn($result);
    }

    /**
     * 更新申请权限
     * @return JsonResponse
     */
    public function updatePermissionApply(Request $request)
    {
        $rules = ['status' => 'required|integer|in:1,2', 'id' => 'required|integer'];
        validatePost($this->post, $rules);
        $_user = $request->get('unauthorized');
        $result = (int)$this->post['status'] === 2 ? $this->permissionApplyService->removePermissionApply($this->post, $_user) : $this->permissionApplyService->updatePermissionApply($this->post, $_user);
        return ajaxReturn($result);
    }
}

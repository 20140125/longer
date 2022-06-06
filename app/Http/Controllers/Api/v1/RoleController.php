<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoleController extends BaseController
{
    /**
     * todo:获取角色信息
     * @param Request $request
     * @return JsonResponse
     */
    public function getRoleLists(Request $request): JsonResponse
    {
        validatePost($request->get('item'), $this->post, ['page' => 'required|integer', 'limit' => 'required|integer']);
        $_user = $request->get('unauthorized');
        $result = $this->roleService->getLists($_user, ['page' => $this->post['page'], 'limit' => $this->post['limit']], ['order' => 'id', 'direction' => 'desc'], ['id', 'role_name', 'status', 'auth_ids', 'created_at', 'updated_at']);
        return ajaxReturn($result);
    }

    /**
     * todo:获取角色权限
     * @return JsonResponse
     */
    public function getRoleAuth(Request $request): JsonResponse
    {
        validatePost($request->get('item'));
        $result = $this->authService->getLists([], ['id as key', 'name as label'], true);
        return ajaxReturn($result);
    }

    /**
     * todo:保存角色
     * @return JsonResponse
     */
    public function saveRole(Request $request): JsonResponse
    {
        validatePost($request->get('item'), $this->post, ['role_name' => 'required|string|unique:os_role', 'auth_ids' => 'required|array', 'status' => 'required|integer|in:1,2']);
        $result = $this->roleService->saveRole($this->post);
        return ajaxReturn($result);
    }

    /**
     * todo:保存角色
     * @return JsonResponse
     */
    public function updateRole(Request $request): JsonResponse
    {
        $rules = !empty($this->post['act']) ? ['status' => 'required|integer|in:1,2', 'id' => 'required|integer'] : ['role_name' => 'required|string', 'auth_ids' => 'required|array', 'status' => 'required|integer|in:1,2', 'id' => 'required|integer'];
        validatePost($request->get('item'), $this->post, $rules);
        $result = $this->roleService->updateRole($this->post);
        return ajaxReturn($result);
    }
}


<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends BaseController
{
    /**
     * todo:获取权限列表
     * @return JsonResponse
     */
    public function getAuthLists(Request $request)
    {
        validatePost($request->get('item'));
        $result = $this->authService->getLists([], ['*'], true);
        return ajaxReturn($result);
    }

    /**
     * todo:获取权限树
     * @return JsonResponse
     */
    public function getAuthTree(Request $request)
    {
        validatePost($request->get('item'));
        $result = $this->authService->getLists(['status' => 1, 'level' => 2, 'id' => '']);
        return ajaxReturn($result);
    }

    /**
     * todo:添加权限
     * @return JsonResponse
     */
    public function saveAuth(Request $request)
    {
        validatePost($request->get('item'), $this->post, ['name' => 'required|string|unique:os_auth', 'href' => 'required|string|unique:os_auth', 'status' => 'required|in:1,2|integer', 'pid' => 'required|integer']);
        $result = $this->authService->saveAuth($this->post);
        return ajaxReturn($result);
    }

    /**
     * todo:更新权限
     * @return JsonResponse
     */
    public function updateAuth(Request $request)
    {
        $rules = ['status' => 'required|in:1,2|integer', 'id' => 'required|integer'];
        if (empty($this->post['act'])) {
            $rules['name'] = 'required|string';
            $rules['href'] = 'required|string';
            $rules['pid'] = 'required|integer';
            $rules['path'] = 'required|string';
            $rules['level'] = 'required|integer';
        }
        validatePost($request->get('item'), $this->post, $rules);
        $result = $this->authService->updateAuth($this->post);
        return ajaxReturn($result);
    }
}

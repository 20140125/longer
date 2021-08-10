<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UsersController extends BaseController
{
    /**
     * todo:获取用户列表
     * @param Request $request
     * @return JsonResponse
     */
    public function getUsersLists(Request $request)
    {
        validatePost($this->post, ['page' => 'required|integer', 'limit' => 'required|integer']);
        $_user = $request->get('unauthorized');
        $result = $this->userService->getUserLists($_user, ['page' => $this->post['page'], 'limit' => $this->post['limit']], ['order' => 'updated_at' ,'direction' => 'desc']);
        return ajaxReturn($result);
    }

    /**
     * todo:获取缓存用户列表
     * @return JsonResponse
     */
    public function getCacheUserLists()
    {
        $result = $this->userService->getCacheUserList();
        return ajaxReturn($result);
    }

    /**
     * todo:更新用户
     * @param Request $request
     * @return JsonResponse
     */
    public function updateUsers(Request $request)
    {
        $rules = [
            'username' => 'required|string',
            'email' => 'required|string|email',
            'phone_number' => 'required|string',
            'avatar_url' => 'required|string|url',
            'id' => 'required|integer',
            'role_id' => 'required|integer',
            'status' => 'required|in:1,2|integer',
            'password' => 'required|string|between:6,32'
        ];
        validatePost($this->post, $rules);
        $_user = $request->get('unauthorized');
        $user = $_user->id === $this->post['id'] ? $_user : $this->userService->getUser(['id' => $this->post['id']]);
        $result = $this->userService->updateUsers($this->post, $user, 'updated users successfully');
        return ajaxReturn($result);
    }
}

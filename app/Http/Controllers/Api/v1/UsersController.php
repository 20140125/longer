<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RedisException;

class UsersController extends BaseController
{
    /**
     * 获取用户列表
     * @param Request $request
     * @return JsonResponse
     */
    public function getUsersLists(Request $request): JsonResponse
    {
        validatePost($this->post, ['page' => 'required|integer', 'limit' => 'required|integer']);
        $unauthorized = $request->get('unauthorized');
        $result = $this->userService->getUserLists($unauthorized, ['page' => $this->post['page'], 'limit' => $this->post['limit']], ['order' => 'updated_at', 'direction' => 'desc'], false, ['*'], $this->post);
        return ajaxReturn($result);
    }

    /**
     * 获取缓存用户列表
     * @return JsonResponse
     */
    public function getCacheUserLists(): JsonResponse
    {
        $result = $this->userService->getCacheUserList();
        return ajaxReturn($result);
    }

    /**
     * 更新用户
     * @param Request $request
     * @return JsonResponse
     * @throws RedisException
     */
    public function updateUsers(Request $request): JsonResponse
    {
        $rules = [
            'username'     => 'required|string',
            'phone_number' => 'required|string',
            'avatar_url'   => 'required|string|url',
            'id'           => 'required|integer',
            'role_id'      => 'required|integer',
            'status'       => 'required|in:1,2|integer',
            'password'     => 'required|string|between:6,32'
        ];
        validatePost($this->post, $rules);
        $unauthorized = $request->get('unauthorized');
        $user = $unauthorized->id === $this->post['id'] ? $unauthorized : $this->userService->getUser(['id' => $this->post['id']]);
        $result = $this->userService->updateUsers($this->post, $user, 'updated users successfully');
        return ajaxReturn($result);
    }
}

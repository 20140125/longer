<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserCenterController extends BaseController
{
    /**
     * 获取用户信息
     * @param Request $request
     * @return JsonResponse
     */
    public function getUserInfo(Request $request): JsonResponse
    {
        validatePost($request->get('item'));
        $_user = $request->get('unauthorized');
        $result = $this->userCenterService->getUserInfo($_user);
        return ajaxReturn($result);
    }

    /**
     * 更新用户信息
     * @param Request $request
     * @return JsonResponse
     */
    public function updateUserInfo(Request $request): JsonResponse
    {
        validatePost(
            $request->get('item'),
            $this->post,
            [
                'u_name'        => 'required|string',
                'id'            => 'required|integer',
                'desc'          => 'required|string|max:128',
                'tags'          => 'required|array|max:128',
                'notice_status' => 'required|integer|in:1,2',
                'user_status'   => 'required|integer|in:1,2',
                'uid'           => 'required|integer',
                'ip_address'    => 'required|array',
                'local'         => 'required|array'
            ]
        );
        $_user = $request->get('unauthorized');
        $result = $this->userCenterService->updateUserInfo($this->post, $_user);
        return ajaxReturn($result);
    }
}

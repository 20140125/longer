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
    public function getUserInfo(Request $request)
    {
        $result = $this->userCenterService->getUserInfo($request->get('unauthorized'));
        return ajaxReturn($result);
    }

    /**
     * 更新用户信息
     * @param Request $request
     * @return JsonResponse
     */
    public function updateUserInfo(Request $request)
    {
        validatePost($this->post,
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
        $result = $this->userCenterService->updateUserInfo($this->post, $request->get('unauthorized'));
        return ajaxReturn($result);
    }
}

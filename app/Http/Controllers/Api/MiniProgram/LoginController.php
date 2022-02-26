<?php

namespace App\Http\Controllers\Api\MiniProgram;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LoginController extends BaseController
{
    /**
     * TODO: 微信获取用户的openid
     * @param Request $request
     * @return JsonResponse
     */
    public function getOpenId(Request $request)
    {
        validatePost($request->get('item'), $this->post, ['code' => 'required|string']);
        $result = $this->loginService->getOpenId($this->post);
        return ajaxReturn($result);
    }

    /**
     * todo:小程序登录
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        validatePost($request->get('item'), $this->post, ['nickName' => 'required|string', 'avatarUrl' => 'required|string', 'code2Session' => 'required|Array']);
        $result = $this->loginService->wxLogin($this->post);
        return ajaxReturn($result);
    }
}

<?php

namespace App\Http\Controllers\Api\MiniProgram;

use Illuminate\Http\JsonResponse;

class LoginController extends BaseController
{
    /**
     * 微信获取用户的openid
     * @return JsonResponse
     */
    public function getOpenId()
    {
        validatePost($this->post, ['code' => 'required|string']);
        $result = $this->loginService->getOpenId($this->post);
        return ajaxReturn($result);
    }

    /**
     * 小程序登录
     * @return JsonResponse
     */
    public function login()
    {
        validatePost($this->post, ['nickName' => 'required|string', 'avatarUrl' => 'required|string', 'code2Session' => 'required|Array']);
        $result = $this->loginService->wxLogin($this->post);
        return ajaxReturn($result);
    }
}

<?php

namespace App\Http\Controllers\Api\MiniProgram;

use Illuminate\Http\JsonResponse;

class LoginController extends BaseController
{
    /**
     * TODO: 微信获取用户的openid
     * @param string code 微信code
     * @return JsonResponse
     */
    public function getOpenId()
    {
        validatePost($this->post, ['code'=>'required|string']);
        $result = $this->loginService->getOpenId($this->post);
        return ajaxReturn($result);
    }
    /**
     * todo:小程序登录
     * @return JsonResponse
     */
    public function login()
    {
        validatePost($this->post, ['nickName'=>'required|string', 'avatarUrl'=>'required|string', 'code2Session'=>'required|Array']);
        $result = $this->loginService->wxLogin($this->post);
        return ajaxReturn($result);
    }
}

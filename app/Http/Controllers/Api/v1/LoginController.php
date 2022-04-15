<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;
use App\Http\Controllers\Utils\RedisClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * TODO:用户登录
 * Class LoginController
 * @package App\Http\Controllers\Api\v1
 */
class LoginController extends BaseController
{
    /**
     * TODO:登录系统
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        $rules = ['email' => 'required|between:8,64|email', 'verify_code' => 'required|integer', 'loginType' => 'required'];
        if ($this->post['loginType'] === 'password') {
            $rules = ['email' => 'required|between:8,64|email', 'password' => 'required|between:6,32|string', 'verify_code' => 'required|size:6|string'];
        }
        validatePost($request->get('item'), $this->post, $rules);
        /* 校验Redis内验证码是否存在 */
        $verifyCode = $this->sendEMailService->getVerifyCode($this->post['loginType'] === 'password' ? $this->post['verify_code'] : $this->post['email'], $this->post['verify_code']);
        if ($verifyCode['code'] === Code::VERIFY_CODE_ERROR) {
            return ajaxReturn($verifyCode);
        }
        $form = array('email' => $this->post['email'] ?? '', 'password' => $this->post['password'] ?? '');
        $users = $this->userService->loginSYS($form);
        return ajaxReturn($users);
    }

    /**
     * TODO:检验登录态
     * @param Request $request
     * @return JsonResponse
     */
    public function checkAuthorized(Request $request)
    {
        validatePost($request->get('item'));
        return ajaxReturn($this->authService->setUnauthorized($request->get('unauthorized'), $request->get('role')));
    }

    /**
     * TODO:验证码上报
     * @return JsonResponse
     */
    public function reportCode(Request $request)
    {
        validatePost($request->get('item'), $this->post, ['verify_code' => 'required']);
        return ajaxReturn($this->userService->setVerifyCode($this->post['verify_code'], $this->post['verify_code']));
    }

    /**
     * TODO:发送邮件
     * @return JsonResponse
     */
    public function sendMail(Request $request)
    {
        validatePost($request->get('item'), $this->post, ['email' => 'required|between:8,64|email']);
        $result = $this->sendEMailService->sendMail($this->post);
        return ajaxReturn($result);
    }

    /**
     * todo:登出系统
     * @return JsonResponse
     */
    public function logout(Request $request)
    {
        validatePost($request->get('item'), $this->post, ['token' => 'required']);
        $result = $this->userService->getUser(['remember_token' => $this->post['token']]);
        if ($result) {
            /* 清空redis数据 */
            RedisClient::getInstance()->del($this->post['token']);
            return ajaxReturn(array('lists' => array('users' => $result), 'message' => 'successfully', 'code' => Code::SUCCESS));
        }
        return ajaxReturn(array('lists' => [], 'message' => 'users not found', 'code' => Code::ERROR));
    }
}

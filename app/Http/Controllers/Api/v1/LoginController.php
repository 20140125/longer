<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;
use App\Http\Controllers\Utils\RedisClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RedisException;

/**
 * 用户登录
 * Class LoginController
 * @package App\Http\Controllers\Api\v1
 */
class LoginController extends BaseController
{
    /**
     * 登录系统
     * @return JsonResponse
     * @throws RedisException
     */
    public function login(): JsonResponse
    {
        $rules = ['email' => 'required|between:8,64|email', 'verify_code' => 'required|integer', 'loginType' => 'required'];
        if ($this->post['loginType'] === 'password') {
            $rules = ['email' => 'required|between:8,64|email', 'password' => 'required|between:6,32|string', 'verify_code' => 'required|size:6|string'];
        }
        validatePost($this->post, $rules);
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
     * 检验登录态
     * @param Request $request
     * @return JsonResponse
     */
    public function checkAuthorized(Request $request): JsonResponse
    {
        return ajaxReturn($this->authService->setUnauthorized($request->get('unauthorized'), $request->get('role')));
    }

    /**
     * 验证码上报
     * @return JsonResponse
     */
    public function reportCode(): JsonResponse
    {
        validatePost($this->post, ['verify_code' => 'required']);
        return ajaxReturn($this->userService->setVerifyCode($this->post['verify_code'], $this->post['verify_code']));
    }

    /**
     * 发送邮件
     * @return JsonResponse
     */
    public function sendMail(): JsonResponse
    {
        validatePost($this->post, ['email' => 'required|between:8,64|email']);
        $result = $this->sendEMailService->sendMail($this->post);
        return ajaxReturn($result);
    }

    /**
     * 登出系统
     * @return JsonResponse
     * @throws RedisException
     */
    public function logout(): JsonResponse
    {
        validatePost($this->post, ['remember_token' => 'required']);
        $result = $this->userService->getUser(['remember_token' => $this->post['remember_token']]);
        if ($result) {
            /* 清空redis数据 */
            RedisClient::getInstance()->del($this->post['remember_token']);
            return ajaxReturn(array('lists' => array('users' => $result), 'message' => 'successfully', 'code' => Code::SUCCESS));
        }
        return ajaxReturn(array('lists' => [], 'message' => 'users not found', 'code' => Code::ERROR));
    }
}

<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * todo:用户登录
 * Class LoginController
 * @package App\Http\Controllers\Api\v1
 */
class LoginController extends BaseController
{
    /**
     * todo:登录系统
     * @return JsonResponse
     */
    public function login()
    {
        if (empty($this->post['loginType'])) {
            return ajaxReturn(array('code' => Code::ERROR, 'message' => 'required params missing'));
        }
        /* 校验Redis内验证码是否存在 */
        $_bool = $this->sendEMailService->getVerifyCode($this->post['verify_code'], $this->post['verify_code']);
        if ($_bool['code'] === Code::VERIFY_CODE_ERROR) {
            return ajaxReturn($_bool);
        }
        $rules = ['email' =>'required|between:8,64|email', 'verify_code' =>'required|size:8|string'];
        if ($this->post['loginType'] === 'password') {
            $rules = ['email' =>'required|between:8,64|email', 'password' =>'required|between:6,32|string', 'verify_code' =>'required|size:6|string'];
        }
        validatePost($this->post, $rules);
        $form = array('email' => $this->post['email'], 'password' => $this->post['password']);
        $users = $this->userService->loginSYS($form);
        return ajaxReturn($users);
    }

    /**
     * todo:检验登录态
     * @param Request $request
     * @return JsonResponse
     */
    public function checkAuthorized(Request $request)
    {
        return ajaxReturn($this->authService->setUnauthorized($request->get('unauthorized'), $request->get('role')));
    }

    /**
     * todo:验证码上报
     * @return JsonResponse
     */
    public function reportCode()
    {
        $this->userService->setVerifyCode($this->post['verify_code'], $this->post['verify_code']);
        return ajaxReturn(array('lists' => array('code' => $this->post['verify_code']), 'message' => 'set verify code successfully', 'code' => Code::SUCCESS ));
    }
}

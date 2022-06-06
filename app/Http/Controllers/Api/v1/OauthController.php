<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OauthController extends BaseController
{
    /**
     * todo:获取授权用户列表
     * @param Request $request
     * @return JsonResponse
     */
    public function getOAuthLists(Request $request): JsonResponse
    {
        validatePost($request->get('item'), $this->post, ['page' => 'required|integer', 'limit' => 'required|integer']);
        $_user = $request->get('unauthorized');
        $result = $this->oAuthService->getUserLists($_user, ['page' => $this->post['page'], 'limit' => $this->post['limit']], $this->post);
        return ajaxReturn($result);
    }

    /**
     * todo:邮箱账号绑定
     * @param Request $request
     * @return JsonResponse
     */
    public function bindEmail(Request $request): JsonResponse
    {
        validatePost($request->get('item'), $this->post, ['email' => 'required|email', 'code' => 'required|integer|between:8,8', 'id' => 'required|integer']);
        /* 校验Redis内验证码是否存在 */
        $verifyCode = $this->sendEMailService->getVerifyCode($this->post['code'], $this->post['code']);
        if ($verifyCode['code'] === Code::VERIFY_CODE_ERROR) {
            return ajaxReturn($verifyCode);
        }
        $result = $this->oAuthService->bindEmailAction($this->post);
        return ajaxReturn($result);
    }
}

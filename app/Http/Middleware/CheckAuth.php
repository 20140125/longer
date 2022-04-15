<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Service\v1\BaseService;
use App\Http\Controllers\Utils\Code;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CheckAuth extends Base
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        Log::error($this->post['token'] . 'AAAAAAABBBBB');
        parent::handle($request, $next);
        if (empty($this->post['token'])) {
            $request->merge(array('item' => array('code' => Code::UNAUTHORIZED, 'message' => Code::NOT_LOGIN_MESSAGE)));
            return $next($request);
        }
        /* todo:用户是否登录 */
        $authorization = $this->userService->getVerifyCode($this->post['token'], $this->post['token']);
        if ($authorization['code'] !== Code::SUCCESS) {
            $request->merge(array('item' => array('code' => Code::UNAUTHORIZED, 'message' => Code::NOT_LOGIN_MESSAGE)));
            return $next($request);
        }
        /* todo: 单次请求记录超过限制 */
        if (!empty($this->post['limit']) && $this->post['limit'] > intval((BaseService::getInstance()->getConfiguration('MaxPageLimit', 'ImageBed')[0]))) {
            $request->merge(array('item' => array('code' => Code::ERROR, 'message' => Code::PAGE_SIZE_MESSAGE)));
            return $next($request);
        }
        /* todo：鉴权获取用户信息 */
        $_user = $this->userService->getUser(['remember_token' => $this->post['token']]) ?? $this->oauthService->getOauth(['remember_token' => $this->post['token']]);
        if (!$this->redisClient->getValue('oauth_register')) {
            /* todo: 非法途径访问 */
            if (empty($request->header('Authorization'))) {
                $request->merge(array('item' => array('code' => Code::UNAUTHORIZED, 'message' => Code::TOKEN_EMPTY_MESSAGE, 'unauthorized' => $_user)));
                return $next($request);
            }
            /* todo: 用户不存在或者验签参数错误 */
            if (empty($_user) || $this->post['token'] !== $request->header('Authorization')) {
                $request->merge(array('item' => array('code' => Code::UNAUTHORIZED, 'message' => Code::TOKEN_EXPIRED_MESSAGE, 'unauthorized' => $_user)));
                return $next($request);
            }
        }
        /* todo:用户被禁用 */
        if ($_user->status === 2) {
            $request->merge(array('item' => array('code' => Code::UNAUTHORIZED, 'message' => Code::USER_DISABLED_MESSAGE), 'unauthorized' => $_user));
            return $next($request);
        }
        /* todo：获取用户角色信息 */
        $_role = $this->roleService->getRole(['id' => $_user->role_id], ['auth_api', 'status']);
        if (empty($_role)) {
            $request->merge(array('item' => array('code' => Code::UNAUTHORIZED, 'message' => Code::ROLE_NOT_EXIST_MESSAGE), 'unauthorized' => $_user));
            return $next($request);
        }
        /* todo:角色被禁用 */
        if ($_role->status === 2) {
            $request->merge(array('item' => array('code' => Code::UNAUTHORIZED, 'message' => Code::ROLE_DISABLED_MESSAGE), 'unauthorized' => $_user));
            return $next($request);
        }
        /* todo: 用户不属于超级管理员 */
        if ($_user->role_id !== 1) {
            /* todo: 角色鉴权 */
            if (!in_array($request->getRequestUri(), json_decode($_role->auth_api, true))) {
                $request->merge(array('item' => array('code' => Code::FORBIDDEN, 'message' => Code::FORBIDDEN_MESSAGE)));
                return $next($request);
            }
        }
        $request->merge(array('role' => $_role, 'unauthorized' => $_user));
        return $next($request);
    }
}

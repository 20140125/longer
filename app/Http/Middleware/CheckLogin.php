<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Utils\Code;
use App\Models\Api\v1\Oauth;
use App\Models\Api\v1\Role;
use App\Models\Api\v1\Users;
use Closure;
use Illuminate\Http\Request;

/**
 * Class checkLogin
 * @package App\Http\Middleware
 */
class CheckLogin extends Base
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed|void
     */
    public function handle(Request $request, Closure $next)
    {
        parent::handle($request, $next);
        /* 默认不鉴权 */
        $permissionArray = empty($this->post['token']) ?
            [
                route('getSystemConfig'),
                route('reportCode'),
                route('login'),
                route('sendMail')
            ] :
            [
                route('reportCode'),
                route('login'),
                route('sendMail')
            ];
        if (in_array(substr_replace(config('app.url'), '', strlen(config('app.url')) - 1).$request->getRequestUri(), $permissionArray)) {
            return $next($request);
        }
        /*  Web页面鉴权 */
        if (empty($this->post['token']) && in_array(
            substr_replace(config('app.url'), '', strlen(config('app.url')) - 1).$request->getRequestUri(),
            [
                route('wxLogin'),
                route('getOpenId'),
                route('lists'),
                route('hotLists'),
                route('newLists'),
                route('getHotKeyWords'),
                route('imageSpiders')
            ]
        )) {
            return $next($request);
        }
        /* 判断用户是登录 */
        $authorization = $this->userService->getVerifyCode($this->post['token'], $this->post['token']);
        if ($authorization['code'] === Code::SUCCESS) {
            /* 用户信息 */
            $_user = Users::getInstance()->getOne(['remember_token' => $this->post['token']]) ?? Oauth::getInstance()->getOne(['remember_token' => $this->post['token']]);
            /* 用户令牌过期 */
            if (empty($_user)) {
                $request->merge(array('item' => array('code' => Code::UNAUTHORIZED, 'message' => Code::USER_NOT_FOUND_MESSAGE)));
                return $next($request);
            }
            /* 角色信息 */
            $_role = Role::getInstance()->getOne(['id' => $_user->role_id], ['auth_api', 'status']);
            /* 刷新用户Redis存储时间 */
            $this->userService->setVerifyCode($this->post['token'], $this->post['token'], config('app.app_refresh_login_time'));
            /* 存储在线用户 */
            if (!$this->redisClient->sIsMember(config('app.redis_user_key'), $_user->uuid)) {
                $this->redisClient->sAdd(config('app.redis_user_key'), $_user->uuid);
            }
            $request->merge(array('unauthorized' => $_user, 'role' => $_role));
            return $next($request);
        }
        $request->merge(array('item' => array('code' => Code::VALID_TOKEN, 'message' => Code::VALID_TOKEN_MESSAGE)));
        return $next($request);
    }
}

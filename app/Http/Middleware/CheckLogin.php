<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Utils\Code;
use App\Models\Api\v1\Auth;
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
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        parent::handle($request, $next);
        /* todo:默认不鉴权 */
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
        /* todo: Web页面鉴权 */
        if (empty($this->post['token']) && in_array(substr_replace(config('app.url'), '', strlen(config('app.url')) - 1).$request->getRequestUri(),
            [
                route('wxLogin'),
                route('getOpenId'),
                route('lists'),
                route('hotLists'),
                route('newLists'),
                route('getHotKeyWords'),
                route('imageSpiders')
            ])) {
            return $next($request);
        }
        /* todo:判断用户是登录 */
        $authorization = $this->userService->getVerifyCode($this->post['token'], $this->post['token']);
        if ($authorization['code'] === Code::SUCCESS) {
            /* todo:用户信息 */
            $_user = Users::getInstance()->getOne(['remember_token' => $this->post['token']]) ?? Auth::getInstance()->getOne(['remember_token' => $this->post['token']]);
            /* todo:用户令牌过期 */
            if (empty($_user)) {
                $request->merge(array('item' => array('code' => Code::UNAUTHORIZED, 'message' => Code::TOKEN_EXPIRED_MESSAGE)));
                return $next($request);
            }
            /* todo:角色信息 */
            $_role = Role::getInstance()->getOne(['id' => $_user->role_id], ['auth_api', 'status']);
            /* todo:刷新用户Redis存储时间 */
            $this->userService->setVerifyCode($this->post['token'], $this->post['token'], config('app.app_refresh_login_time'));
            /* todo:存储在线用户 */
            if (!$this->redisClient->sIsMember(config('app.redis_user_key'), $_user->uuid)) {
                $this->redisClient->sAdd(config('app.redis_user_key'), $_user->uuid);
            }
            $request->merge(array('unauthorized' => $_user, 'role' => $_role));
            return $next($request);
        }
        $request->merge(array('item' => array('code' => Code::UNAUTHORIZED, 'message' => Code::NOT_LOGIN_MESSAGE)));
        return $next($request);
    }
}

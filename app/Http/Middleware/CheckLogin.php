<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Utils\Code;
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
        $permissionArray = empty($this->post['token']) ? ['/api/v1/oauth/config', '/api/v1/report/code', '/api/v1/account/login', '/api/v1/mail/send'] : [ '/api/v1/report/code', '/api/v1/account/login', '/api/v1/mail/send'];
        if (in_array($request->getRequestUri(), $permissionArray)) {
            return $next($request);
        }
        /* todo:H5, 小程序未登录访问页面 */
        if (in_array(substr_replace(config('app.url'), '', strlen(config('app.url')) - 1).$request->getRequestUri(), [route('getImageLists'), route('getHotKeyWords')])) {
            return $next($request);
        }
        /* todo:判断用户是登录 */
        $authorization = $this->userService->getVerifyCode($this->post['token'], $this->post['token']);
        if ($authorization['code'] === Code::SUCCESS && !empty($this->post['token'])) {
            /* todo:用户信息 */
            $_user = $this->userService->getUser(['remember_token' => $this->post['token']]) ?? $this->oauthService->getOauth(['remember_token' => $this->post['token']]);
            /* todo:用户令牌过期 */
            if (empty($_user)) {
                $request->merge(array('item' => array('code' => Code::UNAUTHORIZED, 'message' => Code::TOKEN_EXPIRED_MESSAGE)));
                return $next($request);
            }
            /* todo:角色信息 */
            $_role = $this->roleService->getRole(['id' => $_user->role_id ?? 2], ['auth_api', 'status']);
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

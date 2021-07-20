<?php

namespace App\Http\Middleware;

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
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        parent::handle($request, $next);
        /* todo：鉴权获取用户信息 */
        $_user = $this->userService->getUser(['remember_token' => $this->post['token']]) ?? $this->oauthService->getOauth(['remember_token' => $this->post['token']]);
        if (!$this->redisClient->getValue('oauth_register')) {
            /* todo: 非法途径访问 */
            if (empty($request->header('Authorization'))) {
                $request->merge(array('unauthorized' => array('code' => Code::UNAUTHORIZED, 'message' => 'Token Is Not Provided')));
                return $next($request);
            }
            /* todo: 用户不存在或者验签参数错误 */
            if (empty($_user) || $this->post['token'] !== $request->header('Authorization')) {
                $request->merge(array('unauthorized' => array('code' => Code::UNAUTHORIZED, 'message' => 'Token Is Expired')));
                return $next($request);
            }
        }
        /* todo:用户被禁用 */
        if ($_user->status === 2) {
            $request->merge(array('unauthorized' => array('code' => Code::UNAUTHORIZED, 'message' => 'User Is Disabled')));
            return $next($request);
        }
        /* todo：获取用户角色信息 */
        $_role = $this->roleService->getRole(['id' => $_user->role_id], ['auth_api', 'status']);
        if (empty($_role)) {
            $request->merge(array('unauthorized' => array('code' => Code::UNAUTHORIZED, 'message' => 'Role Is Not Exists')));
            return $next($request);
        }
        /* todo:角色被禁用 */
        if ($_role->status === 2) {
            $request->merge(array('unauthorized' => array('code' => Code::UNAUTHORIZED, 'message' => 'Role Is Disabled')));
            return $next($request);
        }
        /* todo: 用户不属于超级管理员 */
        if ($_user->role_id !== 1) {
            if (!in_array($request->getRequestUri(), json_decode($_role->auth_api, true))) {
                $request->merge(array('unauthorized' => array('code' => Code::FORBIDDEN, 'message' => 'Permission Denied')));
                return $next($request);
            }
        }
        /* todo:存储在线用户 */
        if (!$this->redisClient->sIsMember(config('app.redis_user_key'), $_user->uuid)) {
            $this->redisClient->sAdd(config('app.redis_user_key'), $_user->uuid);
        }
        /* todo:更新redis用户标识 */
        if (!$this->redisClient->getValue($this->post['token'])) {
            $this->redisClient->setValue($this->post['token'], $this->post['token'], ['EX' => config('app.app_refresh_login_time')]);
        }
        $request->merge(array('role' => $_role, 'unauthorized' => $_user));
        return $next($request);
    }
}

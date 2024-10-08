<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Utils\Code;
use Closure;
use Illuminate\Http\Request;

class Common extends Base
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        parent::handle($request, $next);
        $authorization = $this->userService->getVerifyCode($this->post['token'], $this->post['token']);
        if ($authorization['code'] === Code::SUCCESS) {
            $_user = $this->userService->getUser(['remember_token' => $this->post['token']]) ?? $this->oauthService->getOauth(['remember_token' => $this->post['token']]);
            /* 获取用户角色信息 */
            $_role = $this->roleService->getRole(['id' => $_user->role_id ?? 2], ['auth_api']);
            /* 角色鉴权 */
            if (!in_array($request->getRequestUri(), json_decode($_role->auth_api, true))) {
                setCode(Code::FORBIDDEN);
                exit();
            }
            $this->userService->setVerifyCode($this->post['token'], $this->post['token'], config('app.app_refresh_login_time'));
            $request->merge(array('unauthorized' => $_user));
            return $next($request);
        }
        setCode(Code::FORBIDDEN);
        exit();
    }
}

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
        $permissionArray = empty($this->post['token']) ? ['/api/v1/oauth/config', '/api/v1/report/code', '/api/v1/account/login', '/api/v1/mail/send'] : [ '/api/v1/report/code', '/api/v1/account/login', '/api/v1/mail/send'];
        if (in_array($request->getRequestUri(), $permissionArray) || empty($this->post['token'])) {
            return $next($request);
        }
        $authorization = $this->userService->getVerifyCode($this->post['token'], $this->post['token']);
        if ($authorization['code'] === Code::SUCCESS) {
            $_user = $this->userService->getUser(['remember_token' => $this->post['token']]) ?? $this->oauthService->getOauth(['remember_token' => $this->post['token']]);
            $request->merge(array('unauthorized' => $_user));
            return $next($request);
        }
        setCode(Code::FORBIDDEN);
        exit();
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Common extends Base
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
        if ($this->userService->getVerifyCode($this->post['token'], $this->post['token'])){
            $_user = $this->userService->getUser(['remember_token' => $this->post['token']]) ?? $this->oauthService->getOauth(['remember_token' => $this->post['token']]);
            $request->merge(array('unauthorized' => $_user));
            Log::error($request->getRequestUri());
            return $next($request);
        }
        setCode(403);
        exit();
    }
}

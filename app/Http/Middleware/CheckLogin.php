<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Utils\Code;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        if(!$this->post['token']) {
            return $next($request);
        }
        $authorization = $this->userService->getVerifyCode($this->post['token'], $this->post['token']);
        if ($authorization['code'] === Code::SUCCESS){
            return $next($request);
        }
        Log::error($request->getRequestUri());
        setCode(Code::FORBIDDEN);
        exit();
    }
}

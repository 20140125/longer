<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Utils\Code;
use App\Models\Api\v1\SystemConfig;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class CheckOAuth
{
    /**
     * 授权登录中间件
     * @param Request $request
     * @param Closure $next
     * @return JsonResponse|mixed|void
     */
    public function handle(Request $request, Closure $next)
    {
        $oauthConfiguration = SystemConfig::getInstance()->getOne(['name' => 'oauth'], ['children']);
        $oauth = substr($request->getRequestUri(), strripos($request->getRequestUri(), '/') + 1, strlen($request->getRequestUri()));
        foreach (json_decode($oauthConfiguration->children, true) as $item) {
            if (strtoupper($oauth) == strtoupper($item['name'])) {
                /*  卸载授权登录插件 */
                if ($item['status'] == 2) {
                    $arr = array('item' => array('code' => Code::FORBIDDEN, 'message' => Code::FORBIDDEN_MESSAGE));
                    return ajaxReturn($arr);
                }
            }
        }
        return $next($request);
    }
}

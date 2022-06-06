<?php

namespace App\Http\Middleware;

use App\Http\Controllers\Service\v1\AreaService;
use App\Http\Controllers\Service\v1\AuthService;
use App\Http\Controllers\Service\v1\OauthService;
use App\Http\Controllers\Service\v1\RoleService;
use App\Http\Controllers\Service\v1\UserService;
use App\Http\Controllers\Utils\Code;
use App\Http\Controllers\Utils\RedisClient;
use Closure;
use Illuminate\Http\Request;

class Base
{
    /**
     * @var UserService $userService
     */
    protected UserService $userService;
    /**
     * @var OauthService $oauthService
     */
    protected OauthService $oauthService;
    /**
     * @var RoleService $roleService
     */
    protected RoleService $roleService;
    /**
     * @var AuthService $ruleService
     */
    protected AuthService $ruleService;
    /**
     * @var AreaService $areaService
     */
    protected AreaService $areaService;
    /**
     * @var RedisClient $redisClient
     */
    protected RedisClient $redisClient;
    /**
     * @var $post
     */
    protected $post;

    public function __construct(Request $request)
    {
        $this->userService = UserService::getInstance();
        $this->oauthService = OauthService::getInstance();
        $this->roleService = RoleService::getInstance();
        $this->ruleService = AuthService::getInstance();
        $this->redisClient = RedisClient::getInstance();
        $this->areaService = AreaService::getInstance();
        $this->post = $request->post();
    }

    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed|void
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->getMethod() === 'GET') {
            $request->merge(array('item' => array('code' => Code::METHOD_ERROR, 'message' => Code::METHOD_ERROR_MESSAGE)));
            return $next($request);
        }
    }
}

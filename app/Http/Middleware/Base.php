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
     * @var $userService
     */
    protected $userService;
    /**
     * @var $oauthService
     */
    protected $oauthService;
    /**
     * @var $roleService
     */
    protected $roleService;
    /**
     * @var $ruleService
     */
    protected $ruleService;
    /**
     * @var $areaService
     */
    protected $areaService;
    /**
     * @var $redisClient
     */
    protected $redisClient;
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
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->getMethod() === 'GET') {
            setCode(Code::METHOD_ERROR);
            exit();
        }
    }
}

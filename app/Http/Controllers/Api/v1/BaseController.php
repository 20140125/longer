<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Service\v1\AreaService;
use App\Http\Controllers\Service\v1\AuthService;
use App\Http\Controllers\Service\v1\LogService;
use App\Http\Controllers\Service\v1\RoleService;
use App\Http\Controllers\Service\v1\SendEMailService;
use App\Http\Controllers\Service\v1\UserService;
use App\Http\Controllers\Service\v1\TimeLineService;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    /**
     * @var AuthService $authService
     */
    protected $authService;
    /**
     * @var TimeLineService $timeLineService
     */
    protected $timeLineService;

    protected $logService;

    protected $areaService;

    protected $sendEMailService;

    protected $userService;

    protected $roleService;
    /**
     * @var array|string|null $post
     */
    protected $post;

    public function __construct(Request $request)
    {
        date_default_timezone_set('Asia/Shanghai');
        $this->authService = AuthService::getInstance();
        $this->timeLineService = TimeLineService::getInstance();
        $this->logService = LogService::getInstance();
        $this->areaService = AreaService::getInstance();
        $this->sendEMailService = SendEMailService::getInstance();
        $this->userService = UserService::getInstance();
        $this->roleService = RoleService::getInstance();

        $this->post = $request->post();
        unset($this->post['token']);
    }
}

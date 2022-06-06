<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Service\v1\ApiService;
use App\Http\Controllers\Service\v1\AreaService;
use App\Http\Controllers\Service\v1\AuthService;
use App\Http\Controllers\Service\v1\DatabaseService;
use App\Http\Controllers\Service\v1\EmotionService;
use App\Http\Controllers\Service\v1\FileService;
use App\Http\Controllers\Service\v1\InterfaceCategoryService;
use App\Http\Controllers\Service\v1\LogService;
use App\Http\Controllers\Service\v1\OauthService;
use App\Http\Controllers\Service\v1\PermissionApplyService;
use App\Http\Controllers\Service\v1\PushService;
use App\Http\Controllers\Service\v1\RoleService;
use App\Http\Controllers\Service\v1\SendEMailService;
use App\Http\Controllers\Service\v1\SpiderService;
use App\Http\Controllers\Service\v1\SystemConfigService;
use App\Http\Controllers\Service\v1\ToolsService;
use App\Http\Controllers\Service\v1\UserCenterService;
use App\Http\Controllers\Service\v1\UserService;
use App\Http\Controllers\Service\v1\TimeLineService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * @class BaseController
 * @author <fl140125@gmail.com>
 */
class BaseController extends Controller
{
    /**
     * @var AuthService $authService
     */
    protected AuthService $authService;
    /**
     * @var TimeLineService $timeLineService
     */
    protected TimeLineService $timeLineService;
    /**
     * @var LogService $logService
     */
    protected LogService $logService;
    /**
     * @var AreaService $areaService
     */
    protected AreaService $areaService;
    /**
     * @var SendEMailService $sendEMailService
     */
    protected SendEMailService $sendEMailService;
    /**
     * @var UserService $userService
     */
    protected UserService $userService;
    /**
     * @var RoleService $roleService
     */
    protected RoleService $roleService;
    /**
     * @var PermissionApplyService $permissionApplyService
     */
    protected PermissionApplyService $permissionApplyService;
    /**
     * @var FileService $fileService
     */
    protected FileService $fileService;
    /**
     * @var SystemConfigService $systemConfigService
     */
    protected SystemConfigService $systemConfigService;
    /**
     * @var PushService $pushService
     */
    protected PushService $pushService;
    /**
     * @var ApiService $apiService
     */
    protected ApiService $apiService;
    /**
     * @var OauthService $oAuthService
     */
    protected OauthService $oAuthService;
    /**
     * @var UserCenterService $userCenterService
     */
    protected UserCenterService $userCenterService;
    /**
     * @var DatabaseService $databaseService
     */
    protected DatabaseService $databaseService;
    /**
     * @var InterfaceCategoryService $interfaceCategoryService
     */
    protected InterfaceCategoryService $interfaceCategoryService;
    /**
     * @var ToolsService $toolService
     */
    protected ToolsService $toolService;
    /**
     * @var SpiderService $spiderService
     */
    protected SpiderService $spiderService;
    /**
     * @var EmotionService $emotionService
     */
    protected EmotionService $emotionService;
    /**
     * @var array $post
     */
    protected array $post;

    /**
     * @param Request $request
     */
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
        $this->permissionApplyService = PermissionApplyService::getInstance();
        $this->fileService = FileService::getInstance();
        $this->systemConfigService = SystemConfigService::getInstance();
        $this->pushService = PushService::getInstance();
        $this->apiService = ApiService::getInstance();
        $this->oAuthService = OauthService::getInstance();
        $this->userCenterService = UserCenterService::getInstance();
        $this->databaseService = DatabaseService::getInstance();
        $this->interfaceCategoryService = InterfaceCategoryService::getInstance();
        $this->toolService = ToolsService::getInstance();
        $this->spiderService = SpiderService::getInstance();
        $this->emotionService = EmotionService::getInstance();
        $this->post = $request->post();
        unset($this->post['token']);
    }
}

<?php

namespace App\Http\Controllers\Service\v1;

use App\Http\Controllers\Utils\AMap;
use App\Http\Controllers\Utils\Code;
use App\Http\Controllers\Utils\RedisClient;
use App\Models\Api\v1\ApiCategory;
use App\Models\Api\v1\ApiDoc;
use App\Models\Api\v1\ApiLists;
use App\Models\Api\v1\ApiLog;
use App\Models\Api\v1\Area;
use App\Models\Api\v1\Auth;
use App\Models\Api\v1\Emotion;
use App\Models\Api\v1\Log;
use App\Models\Api\v1\Oauth;
use App\Models\Api\v1\PermissionApplyLog;
use App\Models\Api\v1\Push;
use App\Models\Api\v1\PermissionApply;
use App\Models\Api\v1\Role;
use App\Models\Api\v1\SendEMail;
use App\Models\Api\v1\SystemConfig;
use App\Models\Api\v1\TimeLine;
use App\Models\Api\v1\UserCenter;
use App\Models\Api\v1\Users;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * todo:基础服务
 * Class BaseService
 * @package App\Http\Controllers\Service\v1
 */
class BaseService
{
    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    /**
     * @var static $instance
     */
    private static $instance;

    /**
     * @return static
     */
    public static function getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * @var Users $userModel
     */
    protected $userModel;
    /**
     * @var RedisClient $redisClient
     */
    protected $redisClient;

    protected $sendEMailModel;
    /**
     * @var UserCenter $userCenterModel
     */
    protected $userCenterModel;
    /**
     * @var Oauth $oauthModel
     */
    protected $oauthModel;
    /**
     * @var Role $roleModel
     */
    protected $roleModel;
    /**
     * @var Auth $authModel
     */
    protected $authModel;
    /**
     * @var Area $areaModel
     */
    protected $areaModel;
    /**
     * @var TimeLine $timeLineModel
     */
    protected $timeLineModel;
    /**
     * @var Log $logModel
     */
    protected $logModel;
    /**
     * @var PermissionApply $permissionApplyModel
     */
    protected $permissionApplyModel;
    /**
     * @var PermissionApplyLog $permissionApplyLogModel
     */
    protected $permissionApplyLogModel;
    /**
     * @var SystemConfig $systemConfigModel
     */
    protected $systemConfigModel;
    /**
     * @var Push $pushModel
     */
    protected $pushModel;
    /**
     * @var ApiCategory $apiCategoryModel
     */
    protected $apiCategoryModel;
    /**
     * @var ApiDoc $apiDocModel
     */
    protected $apiDocModel;
    /**
     * @var ApiLists $apiListsModel
     */
    protected $apiListsModel;
    /**
     * @var ApiLog $apiLogModel
     */
    protected $apiLogModel;
    /**
     * @var AMap $aMapUtils
     */
    protected $aMapUtils;

    protected $emotionModel;
    /**
     * @var $return
     */
    protected $return;

    public function __construct()
    {
        $this->userModel = Users::getInstance();
        $this->redisClient = RedisClient::getInstance();
        $this->sendEMailModel = SendEMail::getInstance();
        $this->userCenterModel = UserCenter::getInstance();
        $this->oauthModel = Oauth::getInstance();
        $this->roleModel = Role::getInstance();
        $this->authModel = Auth::getInstance();
        $this->areaModel = Area::getInstance();
        $this->timeLineModel = TimeLine::getInstance();
        $this->logModel = Log::getInstance();
        $this->permissionApplyModel = PermissionApply::getInstance();
        $this->permissionApplyLogModel = PermissionApplyLog::getInstance();
        $this->systemConfigModel = SystemConfig::getInstance();
        $this->pushModel = Push::getInstance();
        $this->apiCategoryModel = ApiCategory::getInstance();
        $this->apiDocModel = ApiDoc::getInstance();
        $this->apiListsModel = ApiLists::getInstance();
        $this->apiLogModel = ApiLog::getInstance();
        $this->aMapUtils = AMap::getInstance();
        $this->emotionModel = Emotion::getInstance();

        /* todo:信息输出 */
        $this->return = array('code' => Code::SUCCESS, 'message' => 'successfully', 'lists' => []);
    }

    /**
     * todo:设置验证码
     * @param $key
     * @param $value
     * @param int $timeout
     * @return array
     */
    public function setVerifyCode($key, $value, int $timeout = 120)
    {
        $result = $this->redisClient->setValue($key, strtoupper($value), ['EX' => $timeout]);
        if (!$result) {
            $this->return['code'] = Code::VERIFY_CODE_ERROR;
            $this->return['message'] = 'Failed set verify code ';
            return $this->return;
        }
        $this->return['message'] = 'Successfully set verify code';
        $this->return['lists'] = array('key' => $key, 'value' => $value, 'timeout' => $timeout);
        return $this->return;
    }

    /**
     * todo:获取验证码
     * @param $key
     * @param $value
     * @return array
     */
    public function getVerifyCode($key, $value)
    {
        $result = $this->redisClient->getValue($key) && strtoupper($value) === $this->redisClient->getValue($key);
        if (!$result) {
            $this->return['code'] = Code::VERIFY_CODE_ERROR;
            $this->return['message'] = 'Get verify code failed';
            return $this->return;
        }
        $this->return['message'] = 'Get verify code successfully';
        $this->return['lists'] = array('key' => $key, 'value' => strtoupper($value));
        return $this->return;
    }

    /**
     * todo:设置权限信息
     * @param $_user
     * @param $_role
     * @return array
     */
    public function setUnauthorized($_user, $_role)
    {
        $adCode = request()->ip() === '127.0.0.1' ? 440305 : getCityCode();
        if (!empty($adCode['code']) && $adCode['code'] === Code::SERVER_ERROR) {
            $adCode = 440305;
        }
        \Illuminate\Support\Facades\Log::error(json_encode($_user));
        $area = $this->areaModel->getOne(['code' => $adCode], ['name', 'parent_id', 'info', 'forecast']);
        $province = $this->areaModel->getOne(['id' => $area->parent_id], ['name']);
        $this->return['lists'] = array(
            'auth'              => json_decode(($_role->auth_api ?? ''), true),
            'remember_token'    => $_user->remember_token ?? '',
            'username'          => $_user->username ?? '',
            'socket'            => config('app.socket_url') ?? '',
            'avatar_url'        => ($_user->username ?? '') == 'admin' ? config('app.avatar_url') : $_user->avatar_url,
            'websocket'         => config('app.websocket') ?? '',
            'role_id'           => encrypt($_user->role_id ?? ''),
            'uuid'              => $_user->uuid ?? '',
            'local'             => config('app.url') ?? '',
            'adcode'            => $adCode ?? '',
            'city'              => !empty($province->name) ? $province->name . $area->name : $area->name,
            'room_id'           => '1200',
            'room_name'         => '畅所欲言',
            'id'                => $_user->id ?? '',
            'default_client_id' => config('app.client_id') ?? '',
            'weather'           => json_decode($area->info, true),
            'forecast'          => json_decode($area->forecast, true),
            'email'             => $_user->email ?? '',
            'ip_address'        => request()->ip()
        );
        return $this->return;
    }

    /**
     * todo:设置默认的权限
     * @param array $authIds
     * @return array
     */
    protected function getDefaultAuth(array $authIds = [])
    {
        /* todo：获取默认的权限 */
        $_defaultAuth = Cache::get('_default_permission');
        if (empty($_defaultAuth)) {
            $commonPermissions = $this->systemConfigModel->getOne(['name' => 'CommonPermission'], ['children']);
            $attr = ['key' => 'id', 'ids' => array()];
            foreach (json_decode($commonPermissions->children, true) as $item) {
                $attr = ['key' => $item['name'], 'ids' => explode(',', $item['value'])];
            }
            $_defaultAuth = $this->authModel->getLists([], ['id'], $attr);
            Cache::put('_default_permission', $_defaultAuth, Carbon::now()->addHours(2));
        }
        $_authIds = [];
        foreach ($_defaultAuth as $auth) {
            if (!in_array($auth->id, $authIds)) {
                $_authIds[] = (int)$auth->id;
            }
        }
        foreach ($authIds as $id) {
            if (!in_array($id, $_authIds)) {
                $_authIds[] = (int)$id;
            }
        }
        return $_authIds;
    }

    /**
     * todo:获取用户权限
     * @param $userId
     * @return array
     */
    public function getUserAuth($userId)
    {
        /* todo:获取用户信息 */
        $_user = $this->userModel->getOne(['id' => $userId], ['role_id']);
        /* todo:获取角色信息 */
        $_role = $this->roleModel->getOne(['id' => $_user->role_id], ['auth_api']);
        $_userAuth = json_decode($_role->auth_api, true);
        /* todo:获取所有权限 */
        $_authLists = $this->authModel->getLists([], ['api', 'name', 'level'], ['key' => 'id', 'ids' => array()], ['order' => 'path', 'direction' => 'asc']);
        foreach ($_authLists as &$item) {
            $item->disable = false;
            if (in_array($item->api, $_userAuth)) {
                $item->disable = true;
            }
        }
        $this->return['lists'] = ['authLists' => $_authLists, 'user_id' => $userId];
        return $this->return;
    }

    /**
     * todo:获取角色权限
     * @param $requestAuthID
     * @param int $status
     * @return array
     */
    protected function getRoleAuth($requestAuthID, int $status)
    {
        /* todo:獲取当前记录的信息 */
        $_requestAuth = $this->permissionApplyModel->getOne(['id' => $requestAuthID], ['user_id', 'href']);
        /* todo:获取当前用户权限 */
        $_user = $this->userModel->getOne(['id' => $_requestAuth->user_id], ['role_id', 'id', 'username']);
        /* todo；获取用户的角色信息 */
        $_role_authIds = json_decode($this->roleModel->getOne(['id' => $_user->role_id], ['auth_ids'])->auth_ids, true);
        /* todo:根据地址获取权限信息 */
        $_auth = $this->authModel->getOne(['api' => $_requestAuth->href], ['id']);
        /* todo:更新角色信息 */
        $_authIds = [];
        /* todo:权限续期 */
        if ($status == 1) {
            array_push($_role_authIds, $_auth->id);
            foreach ($_role_authIds as $item) {
                if (!in_array($_auth->id, $_authIds)) {
                    $_authIds[] = (int)$item;
                }
            }
        }
        /* todo:权限收回 */
        if ($status == 2) {
            foreach ($_role_authIds as $item) {
                if ($item != $_auth->id) {
                    $_authIds[] = (int)$item;
                }
            }
        }
        array_multisort($_authIds, SORT_ASC);
        $_authLists = $this->authModel->getLists([], ['api'], ['key' => 'id', 'ids' => $_authIds]);
        $_auth_api = array();
        foreach ($_authLists as $item) {
            $_auth_api[] = $item->api;
        }
        $form['auth_api'] = str_replace("\\", '', json_encode($_auth_api, JSON_UNESCAPED_UNICODE));
        $form['auth_ids'] = json_encode($_authIds, JSON_UNESCAPED_UNICODE);
        $form['updated_at'] = time();
        return ['form' => $form, 'request_auth_id' => $requestAuthID, 'user' => $_user];
    }

    /**
     * TODO：推送站内信息处理
     * @param $form
     * @return array
     */
    protected function webPushMessage($form)
    {
        try {
            $form['state'] = Code::WEBSOCKET_STATE[2];
            /* todo:推送给所有人 */
            if ($form['uuid'] == config('app.client_id')) {
                if (webPush($form['info'])) {
                    $form['state'] = Code::WEBSOCKET_STATE[0];
                }
            }
            /* todo: 推送给个人 */
            if ($this->redisClient->sIsMember(config('app.redis_user_key'), $form['uuid'])) {
                $form['state'] = webPush($form['info'], $form['uuid']) ? Code::WEBSOCKET_STATE[0] : Code::WEBSOCKET_STATE[1];
            }
            return $form;
        } catch (\Exception $exception) {
            \Illuminate\Support\Facades\Log::error($exception->getMessage());
            return ['code' => Code::SERVER_ERROR, 'message' => $exception->getMessage()];
        }
    }
}

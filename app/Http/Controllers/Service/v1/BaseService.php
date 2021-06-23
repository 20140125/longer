<?php

namespace App\Http\Controllers\Service\v1;

use App\Http\Controllers\Utils\Code;
use App\Http\Controllers\Utils\RedisClient;
use App\Models\Api\v1\ApiCategory;
use App\Models\Api\v1\ApiDoc;
use App\Models\Api\v1\ApiLists;
use App\Models\Api\v1\Area;
use App\Models\Api\v1\Auth;
use App\Models\Api\v1\Log;
use App\Models\Api\v1\Oauth;
use App\Models\Api\v1\Push;
use App\Models\Api\v1\RequestAuth;
use App\Models\Api\v1\Role;
use App\Models\Api\v1\SendEMail;
use App\Models\Api\v1\SystemConfig;
use App\Models\Api\v1\TimeLine;
use App\Models\Api\v1\UserCenter;
use App\Models\Api\v1\Users;
use Carbon\Carbon;
use Illuminate\Support\Collection;
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

    protected $requestAuthModel;

    protected $systemConfigModel;

    protected $pushModel;

    protected $apiCategoryModel;

    protected $apiDocModel;

    protected $apiListsModel;
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
        $this->requestAuthModel = RequestAuth::getInstance();
        $this->systemConfigModel = SystemConfig::getInstance();
        $this->pushModel = Push::getInstance();
        $this->apiCategoryModel = ApiCategory::getInstance();
        $this->apiDocModel = ApiDoc::getInstance();
        $this->apiListsModel = ApiLists::getInstance();

        /* todo:信息输出 */
        $this->return = array('code' => Code::SUCCESS, 'message' => 'successfully', 'lists' => []);
    }

    /**
     * todo:设置验证码
     * @param $key
     * @param $value
     * @param int $timeout
     */
    public function setVerifyCode($key, $value, $timeout = 120)
    {
        $this->redisClient->setValue($key, strtoupper($value), ['EX' => $timeout]);
    }

    /**
     * todo:获取验证码
     * @param $key
     * @param $value
     * @return array
     */
    public function getVerifyCode($key, $value)
    {
        $_bool = $this->redisClient->getValue($key) && strtoupper($value) === $this->redisClient->getValue($key);
        return $_bool ? ['code' => Code::SUCCESS, 'message' => 'Get verify code successfully'] : ['code' => Code::VERIFY_CODE_ERROR, 'message' => 'Get verify code failed'];
    }

    /**
     * todo:设置权限信息
     * @param $_user
     * @param $_role
     * @return array
     */
    public function setUnauthorized($_user, $_role)
    {
        $adCode = in_array(getServerIp(), ['192.168.255.10']) ? '440305' : getCityCode();
        $area = $this->areaModel->getOne(['code' => $adCode], ['name', 'parent_id', 'info']);
        $province = $this->areaModel->getOne(['id' => $area->parent_id], ['name']);
        $this->return['lists'] = array(
            'auth' => $_role->auth_url ?? '',
            'remember_token' => $_user->remember_token ?? '',
            'username' => $_user->username ?? '',
            'socket' => config('app.socket_url') ?? '',
            'avatar_url' => $_user->username == 'admin' ? config('app.avatar_url') : $_user->avatar_url,
            'websocket' => config('app.websocket') ?? '',
            'role_id' => encrypt($_user->role_id ?? ''),
            'uuid' => $_user->uuid ?? '',
            'local' => config('app.url') ?? '',
            'adcode' => $adCode ?? '',
            'city' => !empty($province->name) ? $province->name.$area->name : $area->name,
            'room_id' =>'1200',
            'room_name' => '畅所欲言',
            'user_id' => encrypt($_user->id ?? ''),
            'default_client_id' => config('app.client_id') ?? '',
            'weather' => $area->info
        );
        return $this->return;
    }

    /**
     * todo:设置默认的权限
     * @param array $authIds
     * @return Collection|mixed
     */
    public function getDefaultAuth(array $authIds = [])
    {
        /* todo：获取默认的权限 */
        $_defaultAuth = Cache::get('_defaultAuth');
        if (empty($_defaultAuth)) {
            $_defaultAuth = $this->authModel->getLists([], ['id'], ['key' => 'pid', 'ids' => array(100)]);
            Cache::put('_defaultAuth', $_defaultAuth, Carbon::now()->addHours(2));
        }
        foreach ($_defaultAuth as $auth) {
            if (!in_array($auth->id, $authIds)) {
                $auth_ids[] = (int)$auth->id;
            }
        }
        $_authIds = [];
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
        $_role = $this->roleModel->getOne(['id' => $_user->role_id], ['auth_url']);
        $_userAuth = json_decode($_role->auth_url, true);
        /* todo:获取所有权限 */
        $_authLists = Cache::get('auth_lists');
        if (empty($_userLists)) {
            $_authLists = $this->authModel->getLists([], ['href', 'name', 'level'], ['key' => 'id', 'ids' => array()]);
            Cache::put('auth_lists', $_authLists, Carbon::now()->addHour());
        }
        foreach ($_authLists as &$item) {
            $item->disable = false;
            if (in_array($item->href, $_userAuth)) {
                $item->disable = true;
            }
        }
        /* todo:获取所有用户 */
        $_userLists = Cache::get('_user_lists');
        if (empty($_userLists)) {
            $_userLists = $this->userModel->getLists('', [], [], true, ['id', 'username','uuid']);
            Cache::put('_user_lists', $_userLists, Carbon::now()->addHours(2));
        }
        return ['authLists' => $_authLists, 'userLists' => $_userLists, 'user_id' => $userId];
    }

    /**
     * todo:获取角色权限
     * @param $requestAuthID
     * @param int $status
     * @return array
     */
    public function getRoleAuth($requestAuthID, int $status)
    {
        /* todo:獲取当前记录的信息 */
        $_requestAuth = $this->requestAuthModel->getOne(['id' => $requestAuthID], ['user_id', 'href']);
        /* todo:获取当前用户权限 */
        $_user = $this->userModel->getOne(['id' => $_requestAuth->user_id], ['role_id', 'id', 'username']);
        /* todo；获取用户的角色信息 */
        $_role_authIds = json_decode($this->roleModel->getOne(['id' => $_user->role_id], ['auth_ids'])->auth_ids, true);
        /* todo:根据地址获取权限信息 */
        $_auth_id = $this->authModel->getOne(['href' => $_requestAuth->href], ['id'])->id;
        /* todo:更新角色信息 */
        $_authIds = [];
        /* todo:权限续期 */
        if ($status == 1) {
            array_push($_role_authIds, $_auth_id);
            foreach ($_role_authIds as $item) {
                if (!in_array($_auth_id, $_authIds)) {
                    $_authIds[] = (int)$item;
                }
            }
        }
        /* todo:权限收回 */
        if ($status == 2) {
            foreach ($_role_authIds as $item) {
                if ($item != $_auth_id) {
                    $_authIds[] = (int)$item;
                }
            }
        }
        array_multisort($_authIds, SORT_ASC);
        $_authLists = $this->authModel->getLists([], ['href'], ['key' => 'id', 'ids' => $_authIds]);
        $_auth_url = array();
        foreach ($_authLists as $item) {
            $_auth_url[] = $item->href;
        }
        $form['auth_url'] = str_replace("\\", '', json_encode($_auth_url, JSON_UNESCAPED_UNICODE));
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
            $form['state'] = Code::WEBSOCKET_STATE[1];
            /* todo:推送给所有人 */
            if ($form['uuid'] == config('app.client_id')) {
                if (webPush($form['info'])) $form['state'] = Code::WEBSOCKET_STATE[0];
            }
            /* todo: 推送给个人 */
            if ($this->redisClient->sIsMember(config('app.redis_user_key'), $form['uuid'])) {
                $form['state'] = webPush($form['info'], $form['uuid']) ? Code::WEBSOCKET_STATE[0] : Code::WEBSOCKET_STATE[2];
            }
            return $form;
        } catch (\Exception $exception) {
            \Illuminate\Support\Facades\Log::error($exception->getMessage());
            return ['code' => Code::SERVER_ERROR, 'message' => $exception->getMessage()];
        }
    }
}

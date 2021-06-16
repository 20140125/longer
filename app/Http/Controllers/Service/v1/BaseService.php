<?php

namespace App\Http\Controllers\Service\v1;

use App\Http\Controllers\Utils\Code;
use App\Http\Controllers\Utils\RedisClient;
use App\Models\Api\v1\Area;
use App\Models\Api\v1\Auth;
use App\Models\Api\v1\Log;
use App\Models\Api\v1\Oauth;
use App\Models\Api\v1\Role;
use App\Models\Api\v1\SendEMail;
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
        $area = $this->areaModel->getOne(['code' => $adCode], ['name', 'parent_id']);
        $province = $this->areaModel->getOne(['id' => $area->parent_id], ['name']);
        $this->return['lists'] = array(
            'auth' => $_role->auth_url ?? '',
            'remember_token' => $_user->remember_token ?? '',
            'username' => $_user->username ?? '',
            'socket' => config('app.socket_url') ?? '',
            'avatar_url' => $_user->username == 'admin' ? config('app.avatar_url') : $_user->avatar_url,
            'websocket' => config('app.websocket') ?? '',
            'role_id' => md5($_user->role_id ?? ''),
            'uuid' => $_user->uuid ?? '',
            'local' => config('app.url') ?? '',
            'adcode' => $adCode ?? '',
            'city' => !empty($province->name) ? $province->name.$area->name : $area->name,
            'room_id' =>'1200',
            'room_name' => '畅所欲言',
            'user_id' => md5($_user->id ?? ''),
            'default_client_id' => config('app.client_id') ?? ''
        );
        return $this->return;
    }

    /**
     * todo:设置默认的权限
     * @param array $auth_ids
     * @return Collection|mixed
     */
    public function getDefaultAuth(array $auth_ids = [])
    {
        /* todo：获取默认的权限 */
        $_defaultAuth = Cache::get('_defaultAuth');
        if (empty($_defaultAuth)) {
            $_defaultAuth = $this->authModel->getLists([], ['id'], ['key' => 'pid', 'ids' => array(100)]);
            Cache::put('_defaultAuth', $_defaultAuth, Carbon::now()->addHours(2));
        }
        foreach ($_defaultAuth as $auth) {
            if (!in_array($auth->id, $auth_ids)) {
                $auth_ids[] = (int)$auth->id;
            }
        }
        $_authIds = [];
        foreach ($auth_ids as $id) {
            if (!in_array($id, $_authIds)) {
                $_authIds[] = (int)$id;
            }
        }
        return $_authIds;
    }
}

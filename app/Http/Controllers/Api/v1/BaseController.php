<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\CommonController;
use App\Http\Controllers\Utils\Code;
use App\Http\Controllers\Utils\RedisClient;
use App\Http\Controllers\Utils\Rsa;
use App\Http\Controllers\Controller;
use App\Models\OAuth;
use App\Models\Role;
use App\Models\Auth;
use App\Models\Users;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * TODO:公共类
 * Class BaseController
 * @author <fl140125@gmail.com>
 * @package App\Http\Controllers\Api\v1
 */
class BaseController extends Controller
{
    /**
     * @var Users $userModel 用户模型
     */
    protected $userModel;
    /**
     * @var Role $roleModel 角色模型
     */
    protected $roleModel;
    /**
     * @var Auth $authModel 权限模型
     */
    protected $authModel;
    /**
     * @var OAuth $oauthModel 授权模型
     */
    protected $oauthModel;
    /**
     * @var array|string|null $post 请求数据
     */
    protected $post;
    /**
     * @var $users
     */
    protected $users;
    /**
     * @var Role $role 角色信息
     */
    protected $role;
    /**
     * @var RedisClient $redisClient
     */
    protected $redisClient;
    /**
     * @var Rsa $rsa rsa 加密
     */
    protected $rsaUtils;
    /**
     * @var string $backupPath 备份地址
     */
    protected $backupPath;
    /**
     * @var CommonController $commonControl
     */
    protected $commonControl;
    /**
     * @var $defaultAuth
     */
    protected $defaultAuth;

    /**
     * BaseController constructor.
     * * TODO:构造函数
     * BaseController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        date_default_timezone_set("Asia/Shanghai");
        $url = $request->getRequestUri();
        if (strstr($url, '?')) {
            $url = substr($url, 0, findStr($request->getRequestUri(), '?', 2));
        }
        if ($request->isMethod('get') && !in_array(asset($url), [route('download')])) {
            $this->setCode(Code::METHOD_ERROR, 'Method Not Allowed');
        }
        $this->post = $request->post();
        $this->userModel = Users::getInstance();
        $this->roleModel = Role::getInstance();
        $this->authModel = Auth::getInstance();
        $this->oauthModel = OAuth::getInstance();
        $this->rsaUtils = Rsa::getInstance();
        $this->redisClient = RedisClient::getInstance();
        $this->commonControl = CommonController::getInstance();
        $this->backupPath = base_path('database/backup');
        if (!is_dir($this->backupPath)) {
            mkdir($this->backupPath);
        }
        $this->post['token'] = $this->post['token'] ?? ($request->get('token')
                ?? $this->redisClient->getValue('oauth_register'));
        //判断必填字段是否为空
        $validate = Validator::make($this->post, ['token'=>'required|string|size:32']);
       //获取用户信息
        $this->users = $this->userModel->getResult('remember_token', $this->post['token'])
            ?? $this->oauthModel->getResult('remember_token', $this->post['token']);
        //用户第三方授权注册不验证headers
        if (!$this->redisClient->getValue('oauth_register')) {
            //token不正确或为空
            if ($validate->fails() || empty($request->header('Authorization'))) {
                $this->setCode(Code::UNAUTHORIZED, 'Token Is Not Provided');
            }
            //token不正确
            if (empty($this->users) || $this->post['token'] !== mb_substr($request->header('Authorization'), 32, 32)) {
                $this->setCode(Code::UNAUTHORIZED, 'Token Is Expired');
            }
        }
        //用户被禁用
        if ($this->users->status!==1) {
            $this->setCode(Code::UNAUTHORIZED, 'Token Is Invalid');
        }
        //用户不属于超级管理员
        $this->role = $this->roleModel->getResult('id', $this->users->role_id, '=', ['auth_url', 'auth_ids','status']);
        //角色不存在
        if (empty($this->role)) {
            $this->setCode(Code::UNAUTHORIZED, 'Role Is Not Exists');
        }
        //角色被禁用
        if ($this->role->status === 2) {
            $this->setCode(Code::UNAUTHORIZED, 'Role Is Disabled');
        }
        if ($this->users->role_id !== 1) {
            if (!empty($this->role) &&
                !in_array(str_replace(['/api/v1'], ['/admin'], $url), json_decode($this->role->auth_url, true))) {
                $this->setCode(Code::NOT_ALLOW, 'Permission denied');
            }
        }
        $default_auth = Cache::get('default_auth');
        if (!$default_auth) {
            $default_auth = $this->authModel->getResult('pid', [100], 'in', ['href', 'id']);
            Cache::put('default_auth', $default_auth, Carbon::now()->addHours(2));
        }
        $common_url = [];
        foreach ($default_auth as $item) {
            $common_url[] = $item->href;
            $this->defaultAuth[] = (int)$item->id;
        }
        if (!in_array(str_replace(['/api/v1'], ['/admin'], $url), $common_url)) {
            unset($this->post['token']);
        }
    }
    /**
     * TODO:设置code
     * @param $code
     * @param $message
     */
    protected function setCode($code, $message)
    {
        setCode($code);
        exit(json_encode(
            array(
                'code'=>$code,
                'msg'=>$message,
                'url'=>str_replace('/', '', config('app.url')).request()->getRequestUri()
            )
        ));
    }

    /**
     * TODO:数据返回
     * @param $code
     * @param $msg
     * @param array $data
     * @return JsonResponse
     */
    protected function ajaxReturn($code, $msg, $data = array())
    {
        $item = array(
            'code' =>$code,
            'msg' =>$msg,
            'item' =>$data,
        );
        return response()->json($item, $code);
    }

    /**
     * TODO：数据检验
     * @param array $rules
     * @param array $message
     */
    protected function validatePost(array $rules, array $message = [])
    {
        $validate = Validator::make($this->post, $rules, $message);
        if ($validate->fails()) {
            if ($validate->errors()->first() == 'Permission denied') {
                $this->setCode(Code::NOT_ALLOW, $validate->errors()->first());
            }
            $this->setCode(Code::ERROR, $validate->errors()->first());
        }
    }

    /**
     * TODO:公钥加密
     * @param string $data
     * @return string|null
     */
    protected function publicEncrypt(string $data)
    {
        return $this->rsaUtils->publicEncrypt($data);
    }

    /**
     * TODO:私钥解密
     * @param string $data
     * @return null
     */
    protected function privateDecrypt(string $data)
    {
        return $this->rsaUtils->privateDecrypt($data);
    }

    /**
     * TODO：推送站内信息处理
     */
    protected function pushMessage()
    {
        $this->post['state'] = Code::WEBSOCKET_STATE[1];
        $this->post['created_at'] = empty($this->post['created_at'])? time():strtotime($this->post['created_at']);
        if ($this->post['status'] == 1) {
            try {
                //推送给所有人
                if ($this->post['uid'] == config('app.client_id')) {
                    if (webPush($this->post['info'])) {
                        $this->post['state'] = Code::WEBSOCKET_STATE[0];
                    }
                    return ;
                }
                //推送给个人
                if ($this->redisClient->sIsMember(config('app.redis_user_key'), $this->post['uid'])) {
                    if (webPush($this->post['info'], $this->post['uid'])) {
                        $this->post['state'] = Code::WEBSOCKET_STATE[0];
                        return ;
                    }
                    return;
                }
                $this->post['state'] = Code::WEBSOCKET_STATE[2];
            } catch (Exception $e) {
                Log::error("站内信息推送失败：".$e->getMessage());
            }
        }
    }
}

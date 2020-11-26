<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\CommonController;
use App\Http\Controllers\Utils\Code;
use App\Http\Controllers\Utils\RedisClient;
use App\Models\Config;
use App\Models\UserCenter;
use App\Models\Users;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * TODO: 登录
 * Class LoginController
 * @author <fl140125@gmail.com>
 * @package App\Http\Controllers\Api\v1
 */
class LoginController
{
    /**
     * @var Users $userModel
     */
    protected $userModel;
    /**
     * @var Config $configModel
     */
    protected $configModel;
    /**
     * @var RedisClient $redisClient
     */
    protected $redisClient;
    /**
     * @var CommonController $commonControl
     */
    protected $commonControl;
    /**
     * @var array $post
     */
    protected $post;

    public function __construct(Request $request)
    {
        $this->userModel = Users::getInstance();
        $this->configModel = Config::getInstance();
        $this->redisClient = RedisClient::getInstance();
        $this->commonControl = CommonController::getInstance();
        $this->post = $request->post();
        date_default_timezone_set("Asia/Shanghai");
    }

    /**
     * todo:验证码上报
     * @param Request $request
     * @return JsonResponse
     */
    public function setVerifyCode(Request $request)
    {
        if ($request->isMethod('get')) {
            return ajaxReturn(Code::METHOD_ERROR, 'method not allowed');
        }
        $this->redisClient->setValue($this->post['verify_code'], strtoupper($this->post['verify_code']), ['EX'=>120]);
        return ajaxReturn(Code::SUCCESS, 'successfully');
    }
    /**
     * TODO:用户登录
     * @param Request $request (username:用户名，password:密码，verify_code:验证码，loginType:登录类型)
     * @param string username
     * @param string password
     * @param integer verify_code
     * @param string loginType
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        if ($request->isMethod('get')) {
            return ajaxReturn(Code::METHOD_ERROR, 'method not allowed');
        }
        if (empty($this->post['loginType'])) {
            return ajaxReturn(Code::ERROR, 'require params missing');
        }
        switch ($this->post['loginType']) {
            case 'password':
                $validate = Validator::make(
                    $this->post,
                    ['email' =>'required|between:8,64|email', 'password' =>'required|between:6,32|string',
                     'verify_code'=>'required|size:6|string']
                );
                if ($validate->fails()) {
                    return ajaxReturn(Code::ERROR, $validate->errors()->first());
                }
                //不区分大小写(图片验证码)
                if (true != $this->redisClient->getValue($this->post['verify_code']) &&
                    strtoupper($this->post['verify_code'])!= $this->redisClient->getValue($this->post['verify_code'])) {
                    return ajaxReturn(Code::ERROR, 'verify code validate error');
                }
                //删除redis缓存的验证码 (防止恶意访问接口)
                $this->redisClient->del($this->post['verify_code']);
                $result = $this->userModel->loginSYS($this->post);
                break;
            case 'mail':
                $validate = Validator::make(
                    $this->post,
                    ['email' =>'required|between:8,64|email', 'verify_code' =>'required|size:8|string']
                );
                if ($validate->fails()) {
                    return ajaxReturn(Code::ERROR, $validate->errors()->first());
                }
                $result = $this->emailLogin();
                break;
            default:
                return ajaxReturn(Code::ERROR, 'Illegal parameter');
        }
        switch ($result) {
            case Code::ERROR:
                $res = ajaxReturn(Code::ERROR, 'account or password validate error');
                break;
            case Code::NOT_ALLOW:
                $res = ajaxReturn(Code::NOT_ALLOW, 'users not allow login system');
                break;
            case Code::VERIFY_CODE:
                $res = ajaxReturn(Code::ERROR, 'verify code error');
                break;
            case Code::SERVER_ERROR:
                $res = ajaxReturn(Code::SERVER_ERROR, 'server error');
                break;
            default:
                $info = [
                    'href' => '/v1/login',
                    'msg' => $result['logInfo'],
                    'username' => $result['username']
                ];
                actLog($info);
                $res = ajaxReturn(Code::SUCCESS, 'login successfully', $this->setUserInfo($result));
                break;
        }
        return $res;
    }
    /**
     * todo:设置用户信息
     * @param $users
     * @return array
     */
    private function setUserInfo($users)
    {
        return [
            'token'=>$users['token'],
            'username'=>$users['username'],
            'socket'=>config('app.socket_url'),
            'avatar_url' => empty($users['avatar_url']) ? $this->getRandomUsersAvatarUrl() : $users['avatar_url'],
            'websocket'=>config('app.websocket'),
            'role_id' => md5($users['role_id']),
            'uuid' => empty($users['uuid']) ? '' :$users['uuid'],
            'local' => config('app.url'),
        ];
    }

    /**
     * todo:获取用户画像
     * @return int
     */
    private function getRandomUsersAvatarUrl()
    {
        $users = json_decode($this->redisClient->sMembers(config('app.chat_user_key'))[0], true);
        $avatarUrl = [];
        foreach ($users as $user) {
            if ($user['client_name']!=='admin') {
                $avatarUrl[] = $user['client_img'];
            }
        }
        return $avatarUrl[rand(0, count($avatarUrl))]; //排除第一张图片
    }

    /**
     * TODO：邮箱登录
     * @return JsonResponse|int|array
     */
    private function emailLogin()
    {
        if (true != $this->redisClient->getValue($this->post['email'])
            && $this->post['verify_code']!= $this->redisClient->getValue($this->post['email'])) {
            return Code::VERIFY_CODE;
        }
        DB::beginTransaction();
        try {
            $result = $this->userModel->getResult('email', $this->post['email']);
            if (!empty($result)) {
                if ($result->status == 2) {
                    return Code::NOT_ALLOW;
                }
                $result->salt = getRoundNum(8);
                $result->password = md5(md5($result->password).$result->salt);
                $result->remember_token = md5(md5($result->password).$result->salt);
                $result->logInfo = 'email login successfully';
                $this->userModel->updateResult(objectToArray($result), 'id', $result->id);
                UserCenter::getInstance()->updateResult(['token'=>$result->remember_token], 'uid', $result->id);
                return objectToArray($result);
            }
            //注册
            $request = array(
                'ip_address' =>request()->ip(),
                'updated_at' =>time(),'role_id'=>2,
                'avatar_url'=>$this->getRandomUsersAvatarUrl()
            );
            $request['salt'] = getRoundNum(8);
            $request['password'] = md5(md5(getRoundNum(32)).$request['salt']);
            $request['remember_token'] = md5(md5($request['password']).$request['salt']);
            $request['email'] = $this->post['email'];
            $request['phone_number'] = 0;
            $request['created_at'] = time();
            $request['uuid'] = config('app.client_id');
            $request['username'] = explode("@", $this->post['email'])[0];
            $request['status'] = 1;  //允许访问
            $id = $this->userModel->addResult($request);
            //新用户注册生成client_id
            $this->userModel->updateResult(['uuid'=>$request['uuid'].$id], 'id', $id);
            UserCenter::getInstance()->addResult(
                [
                    'u_name'=>$request['username'],
                    'uid'=>$id,'token'=>$request['remember_token'],
                    'notice_status'=>1,
                    'user_status'=>1
                ]
            );
            $request['token'] = $request['remember_token'];
            //更新用户画像
            CommonController::getInstance()->updateUserAvatarUrl();
            //删除redis缓存的验证码，防止恶意登录
            $this->redisClient->del($this->post['email']);
            $request['logInfo'] = 'email register successfully';
            DB::commit();
            return $request;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            DB::rollBack();
            return Code::SERVER_ERROR;
        }
    }
    /**
     * TODO:获取邮箱验证码
     * @param Request $request
     * @param string email
     * @return JsonResponse
     */
    public function email(Request $request)
    {
        if ($request->isMethod('get')) {
            return ajaxReturn(Code::METHOD_ERROR, 'method not allowed');
        }
        $validate = Validator::make($this->post, ['email'=>'required|string|email']);
        if ($validate->fails()) {
            return ajaxReturn(Code::ERROR, $validate->errors()->first());
        }
        $hasEmail = $this->userModel->getResult('email', $this->post['email'], '=', ['email','remember_token']);
        if (!$hasEmail) {
            return ajaxReturn(Code::ERROR, 'email not exists');
        }
        $result = $this->commonControl->sendMail($this->post);
        return $result ? ajaxReturn(Code::SUCCESS, 'email send successfully', $hasEmail)
            : ajaxReturn(Code::ERROR, 'email send failed');
    }

    /**
     * TODO:校验验证码是否正确
     * @param Request $request
     * @param string code 验证码
     * @param integer id
     * @return JsonResponse
     */
    public function code(Request $request)
    {
        if ($request->isMethod('get')) {
            return ajaxReturn(Code::METHOD_ERROR, 'method not allowed');
        }
        $validate = Validator::make(
            $this->post,
            ['email'=>'required|string|email', 'verify_code'=>'required|string|size:8']
        );
        if ($validate->fails()) {
            return ajaxReturn(Code::ERROR, $validate->errors()->first());
        }
        if (true != $this->redisClient->getValue($this->post['email'])
            && $this->post['verify_code']!= $this->redisClient->getValue($this->post['email'])) {
            return ajaxReturn(Code::ERROR, 'code verify error');
        }
        return ajaxReturn(Code::SUCCESS, 'code verify successfully');
    }
    /**
     * TODO：获取配置
     * @param Request $request （name:配置名）
     * @return JsonResponse
     */
    public function config(Request $request)
    {
        if ($request->isMethod('get')) {
            return ajaxReturn(Code::METHOD_ERROR, 'method not allowed');
        }
        $validate = Validator::make($this->post, ['name'=>'required|string']);
        if ($validate->fails()) {
            return ajaxReturn(Code::ERROR, $validate->errors()->first());
        }
        $children = $this->configModel->getResult('name', $this->post['name'], '=', ['children'])->children;
        $intFields = ['status','id','pid'];
        $children = json_decode($children, true) ?? [];
        foreach ($intFields as $int) {
            foreach ($children as &$child) {
                $child[$int] = (int)$child[$int];
            }
        }
        return ajaxReturn(Code::SUCCESS, 'successfully', $children);
    }
    /**
     * TODO:：文件下载
     * @param Request $request （token:用户标识，path:文件路径）
     * @param Response $response
     * @return JsonResponse|BinaryFileResponse
     */
    public function download(Request $request, Response $response)
    {
        $username = $this->userModel->getResult('remember_token', $request->get('token'));
        setCode(Code::NOT_FOUND);
        if (empty($username) || in_array(basename($request->get('path')), ['.env','db.php'])) {
            return ajaxReturn(Code::NOT_FOUND, 'file not found');
        }
        if (file_exists($request->get('path'))) {
            Storage::disk('public')->put(basename($request->get('path')), file_get_contents($request->get('path')));
            //保存到服务器
            return $response::download($request->get('path'), basename($request->get('path')));
        }
        return ajaxReturn(Code::NOT_FOUND, 'permission denied');
    }
    /**
     * TODO:：图床列表
     * @param integer id
     * @return JsonResponse
     */
    public function bed()
    {
        if (empty($this->post['id'])) {
            $lists = getTree(DB::table('os_soogif_type')->get(['name','id','pid']), '0', 'children');
            return ajaxReturn(Code::SUCCESS, 'successfully', $lists);
        }
        $validate = Validator::make($this->post, ['id'=>'required|integer']);
        if ($validate->fails()) {
            return ajaxReturn(Code::ERROR, $validate->errors()->first());
        }
        //判断用户是否是登录用户
        $res = DB::table('os_soogif_type')->where('id', '=', $this->post['id'])->first(['pid']);
        if (!in_array($res->pid, [0, 1, 9, 45]) && empty($this->post['token'])) {
            return ajaxReturn(Code::ERROR, 'Please Login System');
        }
        if (!empty($this->post['token'])) {
            if (empty($this->userModel->getResult('remember_token', $this->post['token']))) {
                return ajaxReturn(Code::ERROR, 'Please Login System');
            }
        }
        $lists['data'] = DB::table('os_soogif')
            ->where('type', '=', $this->post['id'])
            ->get(['id','name as label','type','href as url']);
        $lists['total'] =  DB::table('os_soogif')->where('type', '=', $this->post['id'])->count();
        return ajaxReturn(Code::SUCCESS, 'successfully', $lists);
    }
}

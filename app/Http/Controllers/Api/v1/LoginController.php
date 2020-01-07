<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;
use App\Http\Controllers\Utils\RedisClient;
use App\Models\Config;
use App\Models\UserCenter;
use App\Models\Users;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
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
     * @var array $post
     */
    protected $post;

    public function __construct(Request $request)
    {
        $this->userModel = Users::getInstance();
        $this->configModel = Config::getInstance();
        $this->redisClient = new RedisClient();
        $this->post = $request->post();
        date_default_timezone_set("Asia/Shanghai");
    }

    /**
     * TODO:用户登录
     * @param Request $request (username:用户名，password:密码，verify_code:验证码，loginType:登录类型)
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        if ($request->isMethod('get')){
            return ajax_return(Code::METHOD_ERROR,'method not allow');
        }
        if (empty($this->post['loginType'])) {
            return ajax_return(Code::ERROR,'require params missing');
        }
        switch ($this->post['loginType']) {
            case 'password':
                $validate = Validator::make($this->post, ['email' =>'required|between:8,64|email','password' =>'required|between:6,32|string']);
                if ($validate->fails()){
                    return ajax_return(Code::ERROR,$validate->errors()->first());
                }
                $result = $this->userModel->loginRes($this->post);
                break;
            case 'email':
                $validate = Validator::make($this->post, ['email' =>'required|between:8,64|email','verify_code' =>'required|size:8|string']);
                if ($validate->fails()){
                    return ajax_return(Code::ERROR,$validate->errors()->first());
                }
                $result = $this->emailLogin();
                break;
            default:
                return ajax_return(Code::ERROR,'Illegal parameter');
                break;
        }
        if ($result === Code::ERROR){
            return ajax_return(Code::ERROR,'account or password validate error');
        }
        if ($result === Code::NOT_ALLOW){
            return ajax_return(Code::NOT_ALLOW,'users not allow login system');
        }
        return ajax_return(Code::SUCCESS,'login successfully',$result);
    }

    /**
     * TODO：邮箱登录
     * @return JsonResponse|int|array
     */
    protected function emailLogin()
    {
        if (true != $this->redisClient->getValue($this->post['email']) && $this->post['verify_code']!= $this->redisClient->getValue($this->post['email'])) {
            return ajax_return(Code::ERROR,'verify code error');
        }
        $result = $this->userModel->getResult('email',$this->post['email']);
        if (!empty($result)) {
            if ($result->status == 2){
                return Code::NOT_ALLOW;
            }
            $admin['token'] = $result->remember_token;
            $admin['username'] = $result->username;
            $admin['role_id'] = md5($result->role_id);
            $admin['uuid'] = $result->uuid;
            return $admin;
        }
        //注册
        $request = array('ip_address' =>request()->ip(), 'updated_at' =>time(),'role_id'=>2,'avatar_url'=>config('app.url').'default.png');
        $request['salt'] = get_round_num(8);
        $request['password'] = md5 (md5($request['salt']).$request['salt']);
        $request['remember_token'] = md5 (md5($request['password']).$request['salt']);
        $request['email'] = $this->post['email'];
        $request['phone_number'] = 0;
        $request['created_at'] = time();
        $request['uuid'] = md5($request['password']).uniqid();
        $request['username'] = $this->post['email'];
        $request['status'] = 2;
        $result = $this->userModel->addResult($request);
        UserCenter::getInstance()->addResult(['u_name'=>$request['username'],'uid'=>$result,'token'=>$request['remember_token'],'notice_status'=>1,'user_status'=>1]);
        return $request;
    }
    /**
     * TODO:获取邮箱验证码
     * @param string email
     * @return JsonResponse
     */
    public function email()
    {
        $validate = Validator::make($this->post,['email'=>'required|string|email']);
        if ($validate->fails()) {
            return ajax_return(Code::ERROR,$validate->errors()->first());
        }
        $this->post['verify_code'] = get_round_num(8,'number');
        //验证码保存到redis，10分钟有效
        $this->redisClient->setValue($this->post['email'],$this->post['verify_code'],['EX'=>600]);
        try{
            Mail::to($this->post['email'])->send(new \App\Mail\Login($this->post));
            if (!Mail::failures()) {
                $data = array(
                    'email' => $this->post['email'],
                    'code'  => $this->post['verify_code'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                );
                $result = $this->verifyMailAndCode($data);
                if ($result){
                    return ajax_return(Code::SUCCESS,'email send successfully');
                }
                return ajax_return(Code::ERROR,'email send failed');
            }
            return ajax_return(Code::ERROR,'please enter the correct email address');
        }catch (\Exception $exception){
            return ajax_return(Code::ERROR,$exception->getMessage());
        }
    }

    /**
     * TODO:校验验证码是否正确
     * @param string code 验证码
     * @param integer id
     * @return JsonResponse
     */
    public function code()
    {
        $validate = Validator::make($this->post,['email'=>'required|string|email','verify_code'=>'required|string|size:8']);
        if ($validate->fails()) {
            return ajax_return(Code::ERROR,$validate->errors()->first());
        }
        if (true != $this->redisClient->getValue($this->post['email']) && $this->post['verify_code']!= $this->redisClient->getValue($this->post['email'])) {
            return ajax_return(Code::ERROR,'verify code error');
        }
        $data = array(
            'email' => $this->post['email'],
            'code'  => $this->post['verify_code'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        );
        $result = $this->verifyMailAndCode($data);
        if ($result){
            return ajax_return(Code::SUCCESS,'code verify successfully');
        }
        return ajax_return(Code::ERROR,'code verify failed');
    }

    /**
     * TODO:邮箱和邮箱验证码验证
     * @param $data
     * @return bool|Model|Builder|int|object|null
     */
    protected function verifyMailAndCode($data) {
        $result = DB::table('os_send_email')->where(['email'=>$this->post['email']])->where('updated_at','>=',date('Y-m-d H:i:s',strtotime('-10 minutes')))->first();
        if (!empty($result)) {
            unset($data['created_at']);
            $result = DB::table('os_send_email')->where(['code'=>$this->post['verify_code'],'email'=>$this->post['email']])->update($data);
        } else {
            $result = DB::table('os_send_email')->insert($data);
        }
        return $result;
    }

    /**
     * TODO：获取配置
     * @param Request $request （name:配置名）
     * @return JsonResponse
     */
    public function config(Request $request)
    {
        if ($request->isMethod('get')){
            return ajax_return(Code::METHOD_ERROR,'method not allow');
        }
        $validate = Validator::make($this->post,['name'=>'required|string']);
        if ($validate->fails()){
            return ajax_return(Code::ERROR,$validate->errors()->first());
        }
        $result = $this->configModel->getResult('name',$this->post['name'],'=',['value']);
        return ajax_return(Code::SUCCESS,'successfully',json_decode($result->value,true));
    }
    /**
     * TODO:：文件下载
     * @param Request $request （token:用户标识，path:文件路径）
     * @param Response $response
     * @return JsonResponse|BinaryFileResponse
     */
    public function download(Request $request,Response $response)
    {
        $username = $this->userModel->getResult('remember_token',$request->get('token'));
        if (empty($username)){
            set_code(Code::NOT_FOUND);
            return ajax_return(Code::NOT_FOUND,'permission denied');
        }
        if (file_exists($request->get('path'))){
            set_code(Code::NOT_FOUND);
            return $response::download($request->get('path'),basename($request->get('path')));
        }
        set_code(Code::NOT_FOUND);
        return ajax_return(Code::NOT_FOUND,'permission denied');
    }
}

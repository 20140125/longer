<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;
use App\Models\Config;
use App\Models\OAuth;
use App\Models\UserCenter;
use App\Models\Users;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
     * @var array $post
     */
    protected $post;

    public function __construct(Request $request)
    {
        $this->userModel = Users::getInstance();
        $this->configModel = Config::getInstance();
        $this->post = $request->post();

    }

    /**
     * TODO:用户登录
     * @param Request $request (username:用户名，password:密码)
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        if ($request->isMethod('get')){
            return ajax_return(Code::METHOD_ERROR,'method not allow');
        }
        $validate = Validator::make($this->post, ['email' =>'required|between:8,32|email','password' =>'required|between:6,32|string']);
        if ($validate->fails()){
            return ajax_return(Code::ERROR,$validate->errors()->first());
        }
        $result = $this->userModel->loginRes($this->post);
        if ($result === Code::ERROR){
            return ajax_return(Code::ERROR,'account or password validate error');
        }
        if ($result === Code::NOT_ALLOW){
            return ajax_return(Code::NOT_ALLOW,'users not allow login system');
        }
        return ajax_return(Code::SUCCESS,'login successfully',$result);
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

<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;
use App\Http\Controllers\Utils\Rsa;
use App\Models\Config;
use App\Models\UserCenter;
use App\Models\Users;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
        $validate = Validator::make($this->post, ['username' =>'required|between:4,16|string','password' =>'required|between:6,16|string']);
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
        $where[] = array('u_type',2);
        $where[] = array('u_name',$result['username']);
        UserCenter::getInstance()->updateResult(array('token'=>$result['token'],'type'=>'login'),$where);
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
}

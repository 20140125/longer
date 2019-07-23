<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * todo 登录
 * Class LoginController
 * @package App\Http\Controllers\Api\v1
 */
class LoginController extends BaseController
{
    /**
     * todo 用户登录
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        if ($request->isMethod('get')){
            return $this->ajax_return(Code::METHOD_ERROR,'error');
        }
        $validate = Validator::make($this->post, ['username' =>'required|between:4,16|string','password' =>'required|between:6,16|string']);
        if ($validate->fails()){
            return ajax_return(Code::ERROR,$validate->errors()->first());
        }
        $result = $this->userModel->loginRes($this->post);
        if ($result === Code::ERROR){
            return $this->ajax_return(Code::ERROR,'account or password validate error');
        }
        if ($result === Code::NOT_ALLOW){
            return $this->ajax_return(Code::NOT_ALLOW,'users not allow login system');
        }
        return $this->ajax_return(Code::SUCCESS,'login successfully',$result);
    }

    /**
     * todo 用户合法性
     * @param Request $request
     * @return JsonResponse
     */
    public function check(Request $request)
    {
        if ($request->isMethod('get')){
            return $this->ajax_return(Code::METHOD_ERROR,'error');
        }
        $username = $this->userModel->getResult('access_token',$this->post['token']);
        if (!empty($username)){
            $role = $this->roleModel->getResult('id',$username->role_id);
            return $this->ajax_return(Code::SUCCESS,'permission',['auth'=>$role->auth_url]);
        }
        return $this->ajax_return(Code::ERROR,'permission denied');
    }
    /**
     * todo  退出登陆
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request)
    {
        if ($request->isMethod('get')){
            return $this->ajax_return(Code::METHOD_ERROR,'error');
        }
        $username = $this->userModel->getResult('access_token',$request->post('token'));
        if (!empty($username)){
            $username->access_token = md5(md5($username->password).time());
            $this->userModel->updateResult(object_to_array($username),'access_token',$request->post('token'));
            return $this->ajax_return(Code::SUCCESS,'permission');
        }
        return $this->ajax_return(Code::ERROR,'permission denied');
    }
}

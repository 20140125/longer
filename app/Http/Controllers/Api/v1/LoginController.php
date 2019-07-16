<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * 登录
 * Class LoginController
 * @package App\Http\Controllers\Api\v1
 */
class LoginController extends BaseController
{
    /**
     * 用户登录
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
        $result = $this->adminUserModel->loginRes($this->post);
        if ($result === Code::ERROR){
            return $this->ajax_return(Code::ERROR,'account or password validate error');
        }
        return $this->ajax_return(Code::SUCCESS,'login success',$result);
    }

    /**
     * 用户合法性
     * @param Request $request
     * @return JsonResponse
     */
    public function check(Request $request)
    {
        if ($request->isMethod('get')){
            return $this->ajax_return(Code::METHOD_ERROR,'error');
        }
        $username = $this->adminUserModel->getResult('remember_token',$this->post['token']);
        if (!empty($username)){
            $role = $this->adminRoleModel->getResult('id',$username->role_id);
            return $this->ajax_return(Code::SUCCESS,'permission',['auth'=>$role->auth_url]);
        }
        return $this->ajax_return(Code::ERROR,'permission denied');
    }
    /**
     * 退出登陆
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request)
    {
        if ($request->isMethod('get')){
            return $this->ajax_return(Code::METHOD_ERROR,'error');
        }
        $username = $this->adminUserModel->getResult('remember_token',$request->post('token'));
        if (!empty($username)){
            $username->remember_token = md5(md5($username->password).time());
            $this->adminUserModel->updateResult(object_to_array($username),'remember_token',$request->post('token'));
            return $this->ajax_return(Code::SUCCESS,'permission');
        }
        return $this->ajax_return(Code::ERROR,'permission denied');
    }
}

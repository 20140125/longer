<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\CommonController;
use App\Http\Controllers\Utils\Code;
use App\Models\Users;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

/**
 * todo:密码重置
 * Class ResetPasswordController
 * @author <fl140125@gmail.com>
 * @package App\Http\Controllers\Api\v1
 */
class ResetPasswordController
{
    /**
     * @var CommonController $commonControl
     */
    protected $commonControl;
    /**
     * @var array $post
     */
    protected $post;

    protected $userModel;

    /**
     * todo:构造函数
     * ResetPasswordController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->commonControl = CommonController::getInstance();
        $this->post = $request->post();
        $this->userModel = Users::getInstance();
    }
    /**
     * todo:邮件发送
     * @param Request $request
     * @return JsonResponse
     */
    public function sendMail(Request $request)
    {
        if ($request->isMethod('get')){
            return ajax_return(Code::METHOD_ERROR,'method not allowed');
        }
        $validate = Validator::make($this->post,['email'=>'required|string|email']);
        if ($validate->fails()) {
            return ajax_return(Code::ERROR,$validate->errors()->first());
        }
        $hasEmail = $this->userModel->getResult('email',$this->post['email'],'=',['email','uuid']);
        if (!$hasEmail) {
            return ajax_return(Code::ERROR,'email not exists');
        }
        $result = $this->commonControl->sendMail($this->post);
        if ($result){
            return ajax_return(Code::SUCCESS,'email send successfully',$hasEmail);
        }
        return ajax_return(Code::ERROR,'email send failed');
    }
    /**
     * todo:密码重置
     * @param Request $request
     * @return JsonResponse
     */
    public function resetPass(Request $request)
    {
        if ($request->isMethod('get')){
            return ajax_return(Code::METHOD_ERROR,'method not allowed');
        }
        $validate = Validator::make($this->post,['email'=>'required|string|email','uuid'=>'required|string','password'=>'required|string']);
        if ($validate->fails()) {
            return ajax_return(Code::ERROR,$validate->errors()->first());
        }
        $where[] = ['email',$this->post['email']];
        $where[] = ['uuid',$this->post['uuid']];
        $hasEmail = $this->userModel->getResult($where,null,null,['email','uuid']);
        if (!$hasEmail) {
            return ajax_return(Code::ERROR,'email not exists');
        }
        //添加修改密码记录
        $reset = array(
            'email' => $this->post['email'],
            'token' => $this->post['uuid'],
            'update_at' => time(),
            'created_at' => time()
        );
        $salt = get_round_num(8,'str');
        $data = array(
            'salt' => $salt,
            'password' => md5(md5($this->post['password']).$salt),
            'update_at'=>time()
        );
        $update = $this->userModel->updateResult($data,$where,'');
        if ($update){
            DB::table('os_password_resets')->insert($reset);
            return ajax_return(Code::SUCCESS,'reset password successfully');
        }
        return ajax_return(Code::ERROR,'reset password failed');
    }
}

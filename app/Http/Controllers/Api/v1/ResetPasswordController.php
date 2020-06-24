<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\CommonController;
use App\Http\Controllers\Utils\Code;
use App\Models\Users;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
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
}

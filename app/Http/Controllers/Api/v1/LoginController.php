<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;
use App\Models\Users;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * todo 登录
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
     * @var array $post
     */
    protected $post;

    public function __construct(Request $request)
    {
        $this->userModel = Users::getInstance();
        $this->post = $request->post();

    }

    /**
     * todo 用户登录
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        if ($request->isMethod('get')){
            return ajax_return(Code::METHOD_ERROR,'Permission denied');
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
        return ajax_return(Code::SUCCESS,'login successfully','',$result);
    }
}

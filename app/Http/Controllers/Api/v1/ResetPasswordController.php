<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\CommonController;
use App\Http\Controllers\Utils\Code;
use App\Http\Controllers\Utils\RedisClient;
use App\Models\Push;
use App\Models\Users;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
     * @var array|string|null $post
     */
    protected array|string|null $post;
    /**
     * @var Users $userModel
     */
    protected $userModel;
    /**
     * @var RedisClient $redisClient
     */
    protected $redisClient;

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
        $this->redisClient = RedisClient::getInstance();
    }
    /**
     * todo:密码重置
     * @param Request $request
     * @return JsonResponse
     */
    public function resetPass(Request $request)
    {
        if ($request->isMethod('get')) {
            return ajaxReturn(Code::METHOD_ERROR, 'method not allowed');
        }
        $validate = Validator::make(
            $this->post,
            [
                'email'=>'required|string|email',
                'remember_token'=>'required|string',
                'password'=>'required|string',
                'verify_code'=>'required|integer'
            ]
        );
        if ($validate->fails()) {
            return ajaxReturn(Code::ERROR, $validate->errors()->first());
        }
        if (true != $this->redisClient->getValue($this->post['email']) &&
            $this->post['verify_code']!= $this->redisClient->getValue($this->post['email'])) {
            return ajaxReturn(Code::ERROR, 'verify code error');
        }
        DB::beginTransaction();
        try {
            $where[] = ['email',$this->post['email']];
            $where[] = ['remember_token',$this->post['remember_token']];
            $hasEmail = $this->userModel->getResult($where, '', '', ['username']);
            if (!$hasEmail) {
                return ajaxReturn(Code::ERROR, 'email not exists');
            }
            //添加修改密码记录
            $reset = array(
                'email' => $this->post['email'],
                'token' => $this->post['remember_token'],
                'updated_at' => time(),
                'created_at' => time()
            );
            $salt = getRoundNum(8, 'str');
            $data = array('salt' => $salt, 'password' => md5(md5($this->post['password']).$salt), 'updated_at'=>time());
            $data['remember_token'] = md5($data['password'].$salt);
            $update = $this->userModel->updateResult($data, $where);
            DB::table('os_password_resets')->insert($reset);
            DB::commit();
            return $update ? ajaxReturn(Code::SUCCESS, 'reset password successfully') :
                ajaxReturn(Code::ERROR, 'reset password failed');
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            DB::rollBack();
            return ajaxReturn(Code::ERROR, 'reset password failed');
        }
    }
}

<?php

namespace App\Http\Controllers\Api\MiniProgram;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Service\MiniProgram\ImageService;
use App\Http\Controllers\Service\MiniProgram\LoginService;
use App\Http\Controllers\Service\v1\UserService;
use App\Models\Api\v1\Users;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    /**
     * @var LoginService $loginService
     */
    protected $loginService;
    /**
     * @var ImageService $imageService
     */
    protected $imageService;
    /**
     * @var array $post
     */
    protected $post;

    /**
     * WxUsersController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->loginService = LoginService::getInstance();
        $this->imageService = ImageService::getInstance();
        $this->post = $request->post();
        // 用户标识不为空，并且存在用户信息
        if (!empty($this->post['token']) && Users::getInstance()->getOne(['remember_token' => $this->post['token']])) {
            UserService::getInstance()->setVerifyCode($this->post['token'], $this->post['token'], config('app.app_refresh_login_time'));
        }
    }
}

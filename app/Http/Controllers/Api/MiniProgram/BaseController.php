<?php

namespace App\Http\Controllers\Api\MiniProgram;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Service\MiniProgram\ImageService;
use App\Http\Controllers\Service\MiniProgram\LoginService;
use App\Http\Controllers\Service\v1\UserService;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    protected $loginService;

    protected $imageService;

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
        UserService::getInstance()->setVerifyCode($this->post['token'], $this->post['token'], config('app.app_refresh_login_time'));
    }
}

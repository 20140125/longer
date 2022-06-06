<?php

namespace App\Http\Controllers\Api\MiniProgram;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Service\MiniProgram\ImageService;
use App\Http\Controllers\Service\MiniProgram\LoginService;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    /**
     * @var LoginService $loginService
     */
    protected LoginService $loginService;
    /**
     * @var ImageService $imageService
     */
    protected ImageService $imageService;
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
    }
}

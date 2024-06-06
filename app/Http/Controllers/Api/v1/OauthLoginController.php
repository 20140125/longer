<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Oauth\BaiDuController;
use App\Http\Controllers\Oauth\GiteeController;
use App\Http\Controllers\Oauth\GithubController;
use App\Http\Controllers\Oauth\OsChinaController;
use App\Http\Controllers\Oauth\WeiBoController;
use App\Http\Controllers\Utils\RedisClient;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Oauth\QQController;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Redirector;

/**
 * 第三方授权
 * Class OauthLoginController
 * @author <fl140125@gmail.com>
 * @package App\Http\Controllers\Api\v1
 */
class OauthLoginController extends Controller
{
    /**
     * @var RedisClient $redisClient
     */
    protected RedisClient $redisClient;

    /**
     * OauthLoginController constructor.
     */
    public function __construct()
    {
        $this->redisClient = RedisClient::getInstance();
    }

    /**
     * ：QQ跳转到授权登录页面
     * @param string $source
     * @return Application|RedirectResponse|Redirector
     */
    public function QQ(string $source = '')
    {
        $appId = config('app.qq_appid');
        $appSecret = config('app.qq_secret');
        $QQOauth = QQController::getInstance($appId, $appSecret);
        $url = $QQOauth->getAuthUrl(empty($source) ? 32 : 64);
        $this->redisClient->setValue($QQOauth->state, $QQOauth->state, ['EX' => 60]);
        return redirect($url);
    }

    /**
     * ：Github跳转到授权登录页面
     * @param string $source
     * @return Application|RedirectResponse|Redirector
     */
    public function gitHub(string $source = '')
    {
        $appId = config('app.github_appid');
        $appSecret = config('app.github_secret');
        $gitHubOAuth = GithubController::getInstance($appId, $appSecret);
        $url = $gitHubOAuth->getAuthUrl(empty($source) ? 32 : 64);
        $this->redisClient->setValue($gitHubOAuth->state, $gitHubOAuth->state, ['EX' => 60]);
        return redirect($url);
    }

    /**
     * ：Weibo跳转到授权登录页面
     * @param string $source
     * @return Application|RedirectResponse|Redirector
     */
    public function weibo(string $source = '')
    {
        $appId = config('app.weibo_appid');
        $appSecret = config('app.weibo_secret');
        $weiboOAuth = WeiBoController::getInstance($appId, $appSecret);
        $url = $weiboOAuth->getAuthUrl(empty($source) ? 32 : 64);
        $this->redisClient->setValue($weiboOAuth->state, $weiboOAuth->state, ['EX' => 60]);
        return redirect($url);
    }

    /**
     * ：Gitee跳转到授权登录页面
     * @param string $source
     * @return Application|RedirectResponse|Redirector
     */
    public function gitee(string $source = '')
    {
        $appId = config('app.gitee_appid');
        $appSecret = config('app.gitee_secret');
        $giteeOAuth = GiteeController::getInstance($appId, $appSecret);
        $url = $giteeOAuth->getAuthUrl(empty($source) ? 32 : 64);
        $this->redisClient->setValue($giteeOAuth->state, $giteeOAuth->state, ['EX' => 60]);
        return redirect($url);
    }

    /**
     * ：Gitee跳转到授权登录页面
     * @param string $source
     * @return Application|RedirectResponse|Redirector
     */
    public function baidu(string $source = '')
    {
        $appId = config('app.baidu_appid');
        $appSecret = config('app.baidu_secret');
        $baiDuOauth = BaiDuController::getInstance($appId, $appSecret);
        $url = $baiDuOauth->getAuthUrl(empty($source) ? 32 : 64);
        $this->redisClient->setValue($baiDuOauth->state, $baiDuOauth->state, ['EX' => 60]);
        return redirect($url);
    }

    /**
     * ：OsChina跳转到授权登录页面
     * @param string $source
     * @return Application|RedirectResponse|Redirector
     */
    public function osChina(string $source = '')
    {
        $appId = config('app.os_china_appid');
        $appSecret = config('app.os_china_secret');
        $osChinaOauth = OsChinaController::getInstance($appId, $appSecret);
        $url = $osChinaOauth->getAuthUrl(empty($source) ? 32 : 64);
        $this->redisClient->setValue($osChinaOauth->state, $osChinaOauth->state, ['EX' => 60]);
        return redirect($url);
    }
}

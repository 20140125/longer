<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Oauth\BaiDuController;
use App\Http\Controllers\Oauth\GiteeController;
use App\Http\Controllers\Oauth\GithubController;
use App\Http\Controllers\Oauth\OsChinaController;
use App\Http\Controllers\Oauth\WeiBoController;
use App\Http\Controllers\Utils\RedisClient;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Oauth\QQController;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Redirector;

/**
 * TODO:：第三方授权
 * Class OauthLoginController
 * @author <fl140125@gmail.com>
 * @package App\Http\Controllers\Api\v1
 */
class OauthLoginController extends Controller
{
    /**
     * @var RedisClient $redisClient
     */
    protected $redisClient;

    /**
     * OauthLoginController constructor.
     */
    public function __construct()
    {
        $this->redisClient = RedisClient::getInstance();
    }

    /**
     * TODO:：QQ跳转到授权登录页面
     * @return RedirectResponse|Redirector
     */
    public function QQ()
    {
        $appId = config('app.qq_appid');
        $appSecret = config('app.qq_secret');
        $QQOauth = QQController::getInstance($appId, $appSecret);
        $url = $QQOauth->getAuthUrl();
        $this->redisClient->setValue($QQOauth->state, $QQOauth->state, ['EX' => 60]);
        return redirect($url);
    }

    /**
     * TODO:：Github跳转到授权登录页面
     * @return RedirectResponse|Redirector
     */
    public function gitHub()
    {
        $appId = config('app.github_appid');
        $appSecret = config('app.github_secret');
        $gitHubOAuth = GithubController::getInstance($appId, $appSecret);
        $url = $gitHubOAuth->getAuthUrl();
        $this->redisClient->setValue($gitHubOAuth->state, $gitHubOAuth->state, ['EX' => 60]);
        return redirect($url);
    }

    /**
     * TODO:：Weibo跳转到授权登录页面
     * @return RedirectResponse|Redirector
     */
    public function weibo()
    {
        $appId = config('app.weibo_appid');
        $appSecret = config('app.weibo_secret');
        $weiboOAuth = WeiBoController::getInstance($appId, $appSecret);
        $url = $weiboOAuth->getAuthUrl();
        $this->redisClient->setValue($weiboOAuth->state, $weiboOAuth->state, ['EX' => 60]);
        return redirect($url);
    }

    /**
     * TODO:：Gitee跳转到授权登录页面
     * @return RedirectResponse|Redirector
     */
    public function gitee()
    {
        $appId = config('app.gitee_appid');
        $appSecret = config('app.gitee_secret');
        $giteeOAuth =GiteeController::getInstance($appId, $appSecret);
        $url = $giteeOAuth->getAuthUrl();
        $this->redisClient->setValue($giteeOAuth->state, $giteeOAuth->state, ['EX' => 60]);
        return redirect($url);
    }
    /**
     * TODO:：Gitee跳转到授权登录页面
     * @return RedirectResponse|Redirector
     */
    public function baidu()
    {
        $appId = config('app.baidu_appid');
        $appSecret = config('app.baidu_secret');
        $baiDuOauth = BaiDuController::getInstance($appId, $appSecret);
        $url = $baiDuOauth->getAuthUrl();
        $this->redisClient->setValue($baiDuOauth->state, $baiDuOauth->state, ['EX' => 60]);
        return redirect($url);
    }
    /**
     * TODO:：OsChina跳转到授权登录页面
     * @return RedirectResponse|Redirector
     */
    public function osChina()
    {
        $appId = config('app.os_china_appid');
        $appSecret = config('app.os_china_secret');
        $osChinaOauth = OsChinaController::getInstance($appId, $appSecret);
        $url = $osChinaOauth->getAuthUrl();
        $this->redisClient->setValue($osChinaOauth->state, $osChinaOauth->state, ['EX' => 60]);
        return redirect($url);
    }
}

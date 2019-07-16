<?php
namespace App\Http\Controllers\Api\v1;
use App\Http\Controllers\Oauth\GiteeController;
use App\Http\Controllers\Oauth\GithubController;
use App\Http\Controllers\Oauth\WeiboController;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Oauth\QQController as QQOauth;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Redirector;

/**
 * QQ授权
 * Class QQController
 * @package App\Http\Controllers\Api\v1
 */
class OauthLoginController extends Controller
{
    /**
     * todo：QQ跳转到授权登录页面
     * @return RedirectResponse|Redirector
     */
    public function QQ()
    {
        $appId = config('app.qq_appid');
        $appSecret = config('app.qq_secret');
        $QQOauth = new QQOauth($appId,$appSecret);
        $url = $QQOauth->getAuthUrl();
        session(['qq_state'=>$QQOauth->state]);
        return redirect($url);
    }

    /**
     * todo：Github跳转到授权登录页面
     * @return RedirectResponse|Redirector
     */
    public function gitHub()
    {
        $appId = config('app.github_appid');
        $appSecret = config('app.github_secret');
        $gitHubOAuth = new GithubController($appId,$appSecret);
        $url = $gitHubOAuth->getAuthUrl();
        session(['github_state'=>$gitHubOAuth->state]);
        return redirect($url);
    }

    /**
     * todo：Weibo跳转到授权登录页面
     * @return RedirectResponse|Redirector
     */
    public function weibo()
    {
        $appId = config('app.weibo_appid');
        $appSecret = config('app.weibo_secret');
        $weiboOAuth = new WeiboController($appId,$appSecret);
        $url = $weiboOAuth->getAuthUrl();
        session(['weibo_state'=>$weiboOAuth->state]);
        return redirect($url);
    }

    /**
     * todo：Gitee跳转到授权登录页面
     * @return RedirectResponse|Redirector
     */
    public function gitee()
    {
        $appId = config('app.gitee_appid');
        $appSecret = config('app.gitee_secret');
        $giteeOAuth = new GiteeController($appId,$appSecret);
        $url = $giteeOAuth->getAuthUrl();
        session(['weibo_state'=>$giteeOAuth->state]);
        return redirect($url);
    }
}

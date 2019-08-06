<?php
namespace App\Http\Controllers\Api\v1;
use App\Http\Controllers\Oauth\Gitee;
use App\Http\Controllers\Oauth\Github;
use App\Http\Controllers\Oauth\WeiBo;
use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Oauth\QQ;
use App\Http\Controllers\Controller;
use Illuminate\Routing\Redirector;

/**
 * todo：第三方授权
 * Class QQController
 * @author <fl140125@gmail.com>
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
        $QQOauth = new QQ($appId,$appSecret);
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
        $gitHubOAuth = new Github($appId,$appSecret);
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
        $weiboOAuth = new WeiBo($appId,$appSecret);
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
        $giteeOAuth = new Gitee($appId,$appSecret);
        $url = $giteeOAuth->getAuthUrl();
        session(['weibo_state'=>$giteeOAuth->state]);
        return redirect($url);
    }
}

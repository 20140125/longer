<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Oauth\BaiDuController;
use App\Http\Controllers\Oauth\GiteeController;
use App\Http\Controllers\Oauth\GithubController;
use App\Http\Controllers\Oauth\QQController;
use App\Http\Controllers\Oauth\WeiBoController;
use App\Http\Controllers\Utils\Code;
use App\Http\Controllers\Utils\RedisClient;
use App\Models\OAuth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;

/**
 * Class OauthCallbackController
 * @author <fl140125@gmail.com>
 * @package App\Http\Controllers\Api\v1
 */
class OauthCallbackController
{
    /**
     * @var OAuth $oauthModel
     */
    protected $oauthModel;
    /**
     * @var RedisClient $redisClient
     */
    protected $redisClient;

    /**
     * OauthCallbackController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        if (empty($request->get('code')) || empty($request->get('state'))){
            exit(redirect('/#/login'));
        }
        $this->redisClient = new RedisClient();
        if ($this->redisClient->getValue($request->get('state')) == false) {
            exit(redirect('/#/login'));
        }
        $this->oauthModel = OAuth::getInstance();

    }

    /**
     * TODO：QQ授权登录回调地址
     * @param Request $request
     * @return RedirectResponse|Redirector
     * @throws \Exception
     */
    public function QQ(Request $request)
    {
        $appId = config('app.qq_appid');
        $appSecret = config('app.qq_secret');
        $QQOauth = QQController::getInstance($appId,$appSecret);
        // 1 获取access_token
        $result = $QQOauth->getAccessToken($request->get('code'));
        $this->throwException($result);
        // 2 获取用户信息
        $userInfo = $QQOauth->getUserInfo($result['access_token']);
        $this->throwException($userInfo);
        $data = array(
            'username' =>(string)$userInfo['nickname'],
            'openid' =>(string)$QQOauth->openid,
            'avatar_url' =>empty($userInfo['figureurl_qq_2'])?$userInfo['figureurl_qq_1']:$userInfo['figureurl_qq_2'],
            'access_token' =>(string)$result['access_token'],
            'url' =>empty($userInfo['url'])?'':$userInfo['url'],
            'refresh_token' =>empty($result['refresh_token'])?0:$result['refresh_token'],
            'oauth_type' => 'qq',
            'role_id' => 2,
            'expires' =>time()+$result['expires_in'],
            'remember_token' =>md5(md5($userInfo['nickname']).$QQOauth->openid.time()),
        );
        $where[] = array('openid','=',$QQOauth->openid);
        $where[] = array('oauth_type','=','qq');
        return $this->oauth($data,$where);
    }

    /**
     * TODO：gitHub授权回调
     * @param Request $request
     * @return RedirectResponse|Redirector
     * @throws \Exception
     */
    public function GitHub(Request $request)
    {
        $appId = config('app.github_appid');
        $appSecret = config('app.github_secret');
        $gitHubOAuth = GithubController::getInstance($appId,$appSecret);
        // 1 获取access_token
        $result = $gitHubOAuth->getAccessToken($request->get('code'),$request->get('state'));
        $this->throwException($result);
        // 2 获取用户信息
        $userInfo = $gitHubOAuth->getUserInfo($result['access_token']);
        $this->throwException($userInfo);
        $data = array(
            'username' =>$userInfo['login'],
            'openid' =>(string)$userInfo['id'],
            'avatar_url' =>(string)$userInfo['avatar_url'],
            'access_token' =>(string)$result['access_token'],
            'url' =>empty($userInfo['url'])?'':$userInfo['url'],
            'refresh_token' =>empty($result['refresh_token'])?0:$result['refresh_token'],
            'oauth_type' => 'github',
            'role_id' => 2,
            'expires' =>empty($result['expires_in'])?0:time()+$result['expires_in'],
            'remember_token' =>md5(md5($userInfo['login']).$userInfo['id'].time()),
        );
        $where[] = array('openid','=',(string)$userInfo['id']);
        $where[] = array('oauth_type','=','github');
        return $this->oauth($data,$where);
    }

    /**
     * TODO：Weibo授权回调
     * @param Request $request
     * @return RedirectResponse|Redirector
     * @throws \Exception
     */
    public function gitee(Request $request)
    {
        $appId = config('app.gitee_appid');
        $appSecret = config('app.gitee_secret');
        $giteeOauth = GiteeController::getInstance($appId,$appSecret);
        // 1 获取access_token
        $result = object_to_array($giteeOauth->getAccessToken($request->get('code')));
        $this->throwException($result);
        // 2 获取用户信息
        $userInfo = object_to_array($giteeOauth->getUserInfo($result['access_token']));
        $this->throwException($userInfo);
        $data = array(
            'username' =>(string)$userInfo['name'],
            'openid' =>(string)$userInfo['id'],
            'avatar_url' =>(string)$userInfo['avatar_url'],
            'access_token' =>(string)$result['access_token'],
            'url' =>empty($userInfo['url'])?'':$userInfo['url'],
            'refresh_token' =>empty($result['refresh_token'])?0:$result['refresh_token'],
            'oauth_type' => 'gitee',
            'role_id' => 2,
            'expires' =>empty($result['expires_in'])?0:time()+$result['expires_in'],
            'remember_token' =>md5(md5($userInfo['name']).$userInfo['id'].time()),
        );
        $where[] = array('openid','=',(string)$userInfo['id']);
        $where[] = array('oauth_type','=','gitee');
        return $this->oauth($data,$where);
    }

    /**
     * TODO：Weibo授权回调
     * @param Request $request
     * @return RedirectResponse|Redirector
     * @throws \Exception
     */
    public function weibo(Request $request)
    {
        $appId = config('app.weibo_appid');
        $appSecret = config('app.weibo_secret');
        $weiboOAuth = WeiBoController::getInstance($appId,$appSecret);
        // 1 获取access_token
        $result = $weiboOAuth->getAccessToken($request->get('code'));
        $this->throwException($result);
        // 2 获取用户信息
        $userInfo = $weiboOAuth->getUserInfo($result['access_token'],$result['uid']);
        $this->throwException($userInfo);
        $data = array(
            'username' =>(string)$userInfo['name'],
            'openid' =>(string)$userInfo['id'],
            'avatar_url' =>(string)explode('?',$userInfo['avatar_hd'])[0],
            'access_token' =>(string)$result['access_token'],
            'url' =>empty($userInfo['url'])?'':$userInfo['url'],
            'refresh_token' =>empty($result['refresh_token'])?0:$result['refresh_token'],
            'oauth_type' => 'weibo',
            'role_id' => 2,
            'expires' =>empty($result['expires_in'])?0:time()+$result['expires_in'],
            'remember_token' =>md5(md5($userInfo['name']).$userInfo['id'].time()),
        );
        $where[] = array('openid','=',(string)$userInfo['id']);
        $where[] = array('oauth_type','=','weibo');
        return $this->oauth($data,$where);
    }

    /**
     * TODO：Baidu授权回调
     * @param Request $request
     * @return RedirectResponse|Redirector
     */
    public function baidu(Request $request)
    {
        $appId = config('app.baidu_appid');
        $appSecret = config('app.baidu_secret');
        $baiDuOauth = BaiDuController::getInstance($appId,$appSecret);
        // 1 获取access_token
        $result = $baiDuOauth->getAccessToken($request->get('code'));
        $this->throwException($result);
        // 2 获取用户信息
        $userInfo = $baiDuOauth->getUserInfo($result['access_token']);
        $this->throwException($userInfo);
        $data = array(
            'username' =>(string)$userInfo['uname'],
            'openid' =>(string)$userInfo['uid'],
            'avatar_url' =>(string)"https://tb.himg.baidu.com/sys/portrait/item/{$userInfo['portrait']}",
            'access_token' =>(string)$result['access_token'],
            'url' =>empty($userInfo['url'])?'':$userInfo['url'],
            'refresh_token' =>empty($result['refresh_token'])?0:$result['refresh_token'],
            'oauth_type' => 'baidu',
            'role_id' => 2,
            'expires' =>empty($result['expires_in'])?0:time()+$result['expires_in'],
            'remember_token' =>md5(md5($userInfo['uname']).$userInfo['uid'].time()),
        );
        $where[] = array('openid','=',(string)$userInfo['uid']);
        $where[] = array('oauth_type','=','baidu');
        return $this->oauth($data,$where);
    }

    /**
     * TODO：授权信息添加
     * @param $data
     * @param $where
     * @return RedirectResponse|Redirector
     */
    protected function oauth($data,$where)
    {
        $oauth = $this->oauthModel->getResult($where);
        if (!empty($oauth)){
            $oauthRes =  $this->oauthModel->updateResult($data,$where);
        }else{
            $oauthRes =  $this->oauthModel->addResult($data);
        }
        if (!empty($oauthRes)){
            return redirect('/#/admin/index/'.$data['remember_token']);
        }
        return redirect('/#/login');
    }

    /**
     * TODO：异常捕获
     * @param $response
     * @return RedirectResponse|Redirector
     */
    protected function throwException($response)
    {
        try{
            if (isset($response['code']) && $response['code'] === Code::ERROR) {
                set_code($response['code']);
                return redirect('/#/login');
            }
        }catch (\Exception $exception){
            echo $exception->getMessage();
        }
    }
}

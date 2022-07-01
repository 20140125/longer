<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Oauth\BaiDuController;
use App\Http\Controllers\Oauth\GiteeController;
use App\Http\Controllers\Oauth\GithubController;
use App\Http\Controllers\Oauth\OsChinaController;
use App\Http\Controllers\Oauth\QQController;
use App\Http\Controllers\Oauth\WeiBoController;
use App\Http\Controllers\Service\v1\OauthService;
use App\Http\Controllers\Utils\Code;
use App\Http\Controllers\Utils\RedisClient;
use App\Mail\Register;
use App\Models\Api\v1\Oauth;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;

/**
 * Class OauthCallbackController
 * @author <fl140125@gmail.com>
 * @package App\Http\Controllers\Api\v1
 */
class OauthCallbackController extends Controller
{
    /**
     * @var OAuth $oauthModel
     */
    protected Oauth $oauthModel;
    /**
     * @var RedisClient $redisClient
     */
    protected RedisClient $redisClient;
    /**
     * @var string $state
     */
    protected $state;

    /**
     * OauthCallbackController constructor.
     * @param Request $request (code:第三方授权返回code，state:自定义标识)
     */
    public function __construct(Request $request)
    {
        if (empty($request->get('code')) || empty($request->get('state'))) {
            exit(redirect('/login'));
        }
        $this->redisClient = RedisClient::getInstance();
        if (!$this->redisClient->getValue($request->get('state'))) {
            exit(redirect('/login'));
        }
        $this->state = $this->redisClient->getValue($request->get('state'));
        $this->oauthModel = Oauth::getInstance();
    }

    /**
     * TODO：QQ授权登录回调地址
     * @param Request $request （code）
     * @return RedirectResponse|Redirector
     * @throws Exception
     */
    public function QQ(Request $request)
    {
        $appId = config('app.qq_appid');
        $appSecret = config('app.qq_secret');
        $QQOauth = QQController::getInstance($appId, $appSecret);
        // 1 获取access_token
        $result = $QQOauth->getAccessToken($request->get('code'));
        $this->throwException($result);
        // 2 获取用户信息
        $userInfo = $QQOauth->getUserInfo($result['access_token']);
        $this->throwException($userInfo);
        $data = array(
            'username'       => (string)$userInfo['nickname'] ?? '',
            'openid'         => $QQOauth->openid ?? '',
            'avatar_url'     => empty($userInfo['figureurl_qq_2']) ? $userInfo['figureurl_qq_1'] : $userInfo['figureurl_qq_2'],
            'access_token'   => (string)$result['access_token'] ?? '',
            'url'            => $userInfo['url'] ?? '',
            'refresh_token'  => $result['refresh_token'] ?? '0',
            'oauth_type'     => 'qq',
            'expires'        => time() + ($result['expires_in'] ?? 0),
            'remember_token' => encrypt(md5($userInfo['nickname'] ?? '') . $QQOauth->openid ?? '' . time()),
        );
        $where[] = array('openid', '=', $QQOauth->openid);
        $where[] = array('oauth_type', '=', 'qq');
        return $this->oauth($data, $where);
    }

    /**
     * TODO：gitHub授权回调
     * @param Request $request （code，state）
     * @return RedirectResponse|Redirector
     * @throws Exception
     */
    public function gitHub(Request $request)
    {
        $appId = config('app.github_appid');
        $appSecret = config('app.github_secret');
        $gitHubOAuth = GithubController::getInstance($appId, $appSecret);
        // 1 获取access_token
        $result = $gitHubOAuth->getAccessToken($request->get('code'), $request->get('state'));
        $this->throwException($result);
        // 2 获取用户信息
        $userInfo = $gitHubOAuth->getUserInfo($result['access_token']);
        $this->throwException($userInfo);
        $data = array(
            'username'       => $userInfo['login'] ?? '',
            'openid'         => (string)$userInfo['id'] ?? '',
            'avatar_url'     => (string)$userInfo['avatar_url'] ?? '',
            'access_token'   => (string)$result['access_token'] ?? '',
            'url'            => $userInfo['url'] ?? '',
            'refresh_token'  => $result['refresh_token'] ?? '',
            'oauth_type'     => 'github',
            'expires'        => time() + ($result['expires_in'] ?? 0),
            'remember_token' => encrypt(md5($userInfo['login'] ?? '') . $userInfo['id'] ?? '' . time()),
        );
        $where[] = array('openid', '=', (string)$userInfo['id']);
        $where[] = array('oauth_type', '=', 'github');
        return $this->oauth($data, $where);
    }

    /**
     * TODO：Weibo授权回调
     * @param Request $request （code）
     * @return RedirectResponse|Redirector
     * @throws Exception
     */
    public function gitee(Request $request)
    {
        $appId = config('app.gitee_appid');
        $appSecret = config('app.gitee_secret');
        $giteeOauth = GiteeController::getInstance($appId, $appSecret);
        // 1 获取access_token
        $result = $giteeOauth->getAccessToken($request->get('code'));
        $this->throwException($result);
        // 2 获取用户信息
        $userInfo = $giteeOauth->getUserInfo($result['access_token']);
        $this->throwException($userInfo);
        $data = array(
            'username'       => (string)$userInfo['name'] ?? '',
            'openid'         => (string)$userInfo['id'] ?? '',
            'avatar_url'     => (string)$userInfo['avatar_url'] ?? '',
            'access_token'   => (string)$result['access_token'] ?? '',
            'url'            => $userInfo['url'] ?? '',
            'refresh_token'  => $result['refresh_token'] ?? '0',
            'oauth_type'     => 'gitee',
            'expires'        => time() + ($result['expires_in'] ?? 0),
            'remember_token' => encrypt(md5($userInfo['name'] ?? '') . $userInfo['id'] ?? '' . time()),
        );
        $where[] = array('openid', '=', (string)$userInfo['id']);
        $where[] = array('oauth_type', '=', 'gitee');
        return $this->oauth($data, $where);
    }

    /**
     * TODO：Weibo授权回调
     * @param Request $request （code）
     * @return RedirectResponse|Redirector
     * @throws Exception
     */
    public function weibo(Request $request)
    {
        $appId = config('app.weibo_appid');
        $appSecret = config('app.weibo_secret');
        $weiboOAuth = WeiBoController::getInstance($appId, $appSecret);
        // 1 获取access_token
        $result = $weiboOAuth->getAccessToken($request->get('code'));
        $this->throwException($result);
        // 2 获取用户信息
        $userInfo = $weiboOAuth->getUserInfo($result['access_token'], $result['uid']);
        $this->throwException($userInfo);
        $data = array(
            'username'       => (string)$userInfo['name'] ?? '',
            'openid'         => (string)$userInfo['id'] ?? '',
            'avatar_url'     => !empty($userInfo['avatar_hd']) ? (string)explode('?', $userInfo['avatar_hd'])[0] : '',
            'access_token'   => (string)$result['access_token'] ?? '',
            'url'            => $userInfo['url'] ?? '',
            'refresh_token'  => $result['refresh_token'] ?? '0',
            'oauth_type'     => 'weibo',
            'expires'        => time() + ($result['expires_in'] ?? 0),
            'remember_token' => encrypt(md5($userInfo['name'] ?? '') . $userInfo['id'] ?? '' . time()),
        );
        $where[] = array('openid', '=', (string)$userInfo['id']);
        $where[] = array('oauth_type', '=', 'weibo');
        return $this->oauth($data, $where);
    }

    /**
     * TODO：Baidu授权回调
     * @param Request $request （code）
     * @return RedirectResponse|Redirector
     */
    public function baidu(Request $request)
    {
        $appId = config('app.baidu_appid');
        $appSecret = config('app.baidu_secret');
        $baiDuOauth = BaiDuController::getInstance($appId, $appSecret);
        // 1 获取access_token
        $result = $baiDuOauth->getAccessToken($request->get('code'));
        $this->throwException($result);
        // 2 获取用户信息
        $userInfo = $baiDuOauth->getUserInfo($result['access_token']);
        $this->throwException($userInfo);
        $data = array(
            'username'       => (string)$userInfo['username'] ?? '',
            'openid'         => (string)$userInfo['openid'],
            'avatar_url'     => !empty($userInfo['portrait']) ? 'http://tb.himg.baidu.com/sys/portrait/item/' . $userInfo['portrait'] : '',
            'access_token'   => (string)$result['access_token'] ?? '',
            'url'            => $userInfo['url'] ?? '',
            'refresh_token'  => $result['refresh_token'] ?? '0',
            'oauth_type'     => 'baidu',
            'expires'        => time() + ($result['expires_in'] ?? 0),
            'remember_token' => encrypt(md5($userInfo['username'] ?? '') . $userInfo['openid'] ?? '' . time()),
        );
        $where[] = array('openid', '=', (string)$userInfo['openid']);
        $where[] = array('oauth_type', '=', 'baidu');
        return $this->oauth($data, $where);
    }

    /**
     *  TODO：OsChina授权回调
     * @param Request $request （code）
     * @return RedirectResponse|Redirector
     * @throws Exception
     */
    public function osChina(Request $request)
    {
        $appId = config('app.os_china_appid');
        $appSecret = config('app.os_china_secret');
        $osChinaOAuth = OsChinaController::getInstance($appId, $appSecret);
        // 1 获取access_token
        $result = $osChinaOAuth->getAccessToken($request->get('code'));
        $this->throwException($result);
        // 2 获取用户信息
        $userInfo = $osChinaOAuth->getUserInfo($result['access_token']);
        $this->throwException($userInfo);
        $data = array(
            'username'       => (string)$userInfo['name'] ?? '',
            'openid'         => (string)$userInfo['id'] ?? '',
            'avatar_url'     => $userInfo['avatar'] ?? '',
            'access_token'   => (string)$result['access_token'] ?? '',
            'url'            => $userInfo['url'] ?? '',
            'refresh_token'  => $result['refresh_token'] ?? '',
            'oauth_type'     => 'os_china',
            'expires'        => time() + ($result['expires_in'] ?? 0),
            'remember_token' => encrypt(md5($userInfo['name'] ?? '') . $userInfo['id'] ?? '' . time()),
        );
        $where[] = array('openid', '=', (string)$userInfo['id']);
        $where[] = array('oauth_type', '=', 'osChina');
        return $this->oauth($data, $where);
    }

    /**
     * TODO：授权信息添加
     * @param $data
     * @param $where
     * @return RedirectResponse|Redirector
     */
    protected function oauth($data, $where)
    {
        $data['updated_at'] = time();
        $oauth = $this->oauthModel->getOne($where);
        /* 换成用户登录标识（脚本缓存有时间延时） */
        OauthService::getInstance()->setVerifyCode($data['remember_token'], $data['remember_token'], config('app.app_refresh_login_time'));
        if (!empty($oauth)) {
            $oauthRes = $this->oauthModel->updateOne($where, $data);
        } else {
            $data['uid'] = 0;
            $data['role_id'] = 2;
            $data['created_at'] = time();
            $oauthRes = $this->oauthModel->saveOne($data);
            if (!empty($oauthRes)) {
                Artisan::call("longer:sync-oauth {$data['remember_token']}");
                Mail::to(config('app.username'))->send(new Register(array('name' => $data['username'])));
            }
        }
        if (!empty($oauthRes)) {
            $this->redisClient->setValue('oauth_register', $data['remember_token'], ['EX' => 60]);
            // 根据state的长度区分是客户端还是PC端
            return strlen($this->state) == 32 ? redirect('/admin/home/index/' . $data['remember_token'])->send() : redirect('/#/?token='.rawurlencode($data['remember_token']))->send();
        }
        return redirect('/login')->send();
    }

    /**
     * TODO：异常捕获
     * @param $response
     * @return RedirectResponse|Redirector|boolean
     */
    protected function throwException($response)
    {
        if (isset($response['code']) && $response['code'] === Code::ERROR) {
            setCode(Code::ERROR);
            return redirect('/login');
        }
        return true;
    }
}

<?php

namespace App\Http\Controllers\Oauth;

use App\Http\Controllers\Utils\Code;
use Illuminate\Support\Facades\Log;

/**
 * Class GithubController
 * @author <fl140125@gmail.com>
 * @package App\Http\Controllers\Oauth
 */
class GithubController extends OAuthController
{
    /**
     * @var string $appid
     */
    protected $appid;
    /**
     * @var string $appsecret
     */
    protected $appsecret;
    /**
     * @var string $redirectUri
     */
    protected $redirectUri;
    /**
     * @var static $instance
     */
    protected static $instance;
    /**
     * @var string API 业务域名
     */
    protected $apiUrl = 'https://github.com/';

    /**
     * GithubController constructor.
     * @param string $appid
     * @param string $appsecret
     */
    public function __construct(string $appid, string $appsecret)
    {
        parent::__construct();
        $this->appid = $appid;
        $this->appsecret = $appsecret;
        $this->redirectUri = config('app.url').'api/v1/callback/github';
    }

    /**
     * @param string $appid
     * @param string $appsecret
     * @return GithubController
     */
    public static function getInstance(string $appid, string $appsecret)
    {
        if (!self::$instance instanceof self) {
            self::$instance = new static($appid, $appsecret);
        }
        return self::$instance;
    }

    /**
     * TODO:：获取github授权页面
     * @param int $length
     * @param string $callback
     * @param string $scope
     * @return string
     */
    public function getAuthUrl($length = 32, $callback = '', $scope = 'user:email')
    {
        $arr = [
            'client_id' => $this->appid,
            'redirect_uri' => empty($callback) ? $this->redirectUri : $callback,
            'scope' => $scope,  // user:email read:user user:follow
            'state' => $this->getState($length),
            'allow_signup' => true, //是否在登录页显示注册，默认false
        ];
        return $this->apiUrl.'login/oauth/authorize?'.http_build_query($arr);
    }

    /**
     * TODO:：获取access_token
     * @param string $code
     * @param string $state
     * @return array|bool|mixed
     * @throws \Exception
     */
    public function getAccessToken(string $code, string $state)
    {
        $arr = [
            'client_id' => $this->appid,
            'client_secret' => $this->appsecret,
            'code' => $code,
            'redirect_uri' => $this->redirectUri,
            'state' => $state
        ];
        $result = $this->curl->post($this->apiUrl.'login/oauth/access_token', $arr);
        if (!$result) {
            return $this->error(Code::ERROR, 'request interface failed');
        }
        $result = $this->__getAccessToken($result);
        return isset($result['error']) ? $this->error(Code::ERROR, $result['error_description']) : $result;
    }

    /**
     * TODO:：获取用户信息
     * @param string $access_token
     * @return mixed
     * @throws \Exception
     */
    public function getUserInfo(string $access_token)
    {
        $this->curl->setHeader("Authorization", "token $access_token");
        $result = $this->curl->get('https://api.github.com/user');
        if (!$result) {
            return $this->error(Code::ERROR, 'request interface failed');
        }
        $result = objectToArray($result);
        return isset($result['message']) ? $this->error(Code::ERROR, $result['message']) : $result;
    }

    /**
     * TODO:：获取用户信息(包含所有项目)
     * @param string $access_token
     * @return mixed
     * @throws \Exception
     */
    public function getUserRepos(string $access_token)
    {
        $this->curl->setHeader("Authorization", "token $access_token");
        $result = $this->curl->get('https://api.github.com/user/repos');
        Log::error(json_encode($result));
        if (!$result) {
            return $this->error(Code::ERROR, 'request interface failed');
        }
        $result = objectToArray($result);
        return isset($result['message']) ? $this->error(Code::ERROR, $result['message']) : $result;
    }
}

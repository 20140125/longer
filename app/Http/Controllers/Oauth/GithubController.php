<?php

namespace App\Http\Controllers\Oauth;

use App\Http\Controllers\Utils\Code;

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
     * @param $appid
     * @param $appsecret
     */
    public function __construct($appid,$appsecret)
    {
        parent::__construct();
        $this->appid = $appid;
        $this->appsecret = $appsecret;
        $this->redirectUri = config('app.url').'api/v1/callback/github';
    }

    /**
     * @param $appid
     * @param $appsecret
     * @return GithubController
     */
    static public function getInstance($appid,$appsecret)
    {
        if (!self::$instance instanceof self) {
            self::$instance = new static($appid,$appsecret);
        }
        return self::$instance;
    }

    /**
     * TODO:：获取github授权页面
     * @param string $callback
     * @param int $length
     * @param string $scope
     * @return string
     */
    public function getAuthUrl($callback = '', $length = 32, $scope = 'user:email')
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
     * @param $code
     * @param $state
     * @return array|bool|mixed
     * @throws \Exception
     */
    public function getAccessToken($code,$state)
    {
        $arr = [
            'client_id' => $this->appid,
            'client_secret' => $this->appsecret,
            'code' => $code,
            'redirect_uri' => $this->redirectUri,
            'state' => $state
        ];
        $result = $this->curl->post($this->apiUrl.'login/oauth/access_token',$arr);
        if (!$result){
            return $this->error(Code::ERROR,'request interface failed');
        }
        $result = $this->__getAccessToken($result);
        if (isset($result['error'])){
            return $this->error(Code::ERROR,$result['error_description']);
        }
        return $result;

    }

    /**
     * TODO:：获取用户信息
     * @param $access_token
     * @return mixed
     * @throws \Exception
     */
    public function getUserInfo($access_token)
    {
        $result = $this->curl->get('https://api.github.com/user?access_token='.$access_token);
        if (!$result){
            return $this->error(Code::ERROR,'request interface failed');
        }
        $result = object_to_array($result);
        if (isset($result['message'])){
            return $this->error(Code::ERROR,'Get user failed');
        }
        return $result;
    }
}

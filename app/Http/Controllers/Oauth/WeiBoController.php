<?php

namespace App\Http\Controllers\Oauth;

use App\Http\Controllers\Utils\Code;
use Exception;

/**
 * Class WeiboController
 * @author <fl140125@gmail.com>
 * @package App\Http\Controllers\Oauth
 */
class WeiBoController extends OAuthController
{
    /**
     * @var string $appid
     */
    protected $appid;
    /**
     * @var string $appSecret
     */
    protected $appSecret;
    /**
     * @var string $redirectUri
     */
    protected $redirectUri;
    /**
     * @var string $apiUrl 授权业务域名
     */
    protected $apiUrl = 'https://api.weibo.com/';
    /**
     * @var static $instance
     */
    protected static $instance;

    /**
     * WeiboController constructor.
     * @param string $appid
     * @param string $appSecret
     */
    public function __construct(string $appid, string $appSecret)
    {
        parent::__construct();
        $this->appid = $appid;
        $this->appSecret = $appSecret;
        $this->redirectUri = config('app.url') . 'api/v1/callback/weibo';
    }

    /**
     * @param string $appid
     * @param string $appSecret
     * @return WeiBoController
     */
    public static function getInstance(string $appid, string $appSecret): WeiBoController
    {
        if (!self::$instance instanceof self) {
            self::$instance = new static($appid, $appSecret);
        }
        return self::$instance;
    }

    /**
     * TODO:：获取授权登录URL
     * @param int $length
     * @param string $callback
     * @param string $scope
     * @return string
     */
    public function getAuthUrl(int $length = 32, string $callback = '', string $scope = 'all,email'): string
    {
        $this->redirectUri = empty($callback) ? $this->redirectUri : $callback;
        $arr = [
            'client_id'    => $this->appid,
            'redirect_uri' => $this->redirectUri,
            'scope'        => $scope,
            'state'        => $this->getState($length),
            'display'      => 'default',  // default mobile wap client
            'forcelogin'   => false, //是否强制用户重新登录，true：是，false：否。默认false。
            'language'     => ''  //授权页语言，缺省为中文简体版，en为英文版
        ];
        return $this->apiUrl . 'oauth2/authorize?' . http_build_query($arr);
    }

    /**
     * TODO:：获取access_token
     * @param string $code
     * @return array
     * @throws Exception
     */
    public function getAccessToken(string $code): array
    {
        $arr = [
            'client_id'     => $this->appid,
            'client_secret' => $this->appSecret,
            'grant_type'    => 'authorization_code',
            'code'          => $code,
            'redirect_uri'  => $this->redirectUri
        ];
        $result = $this->curl->post($this->apiUrl . 'oauth2/access_token', $arr);
        if (!$result) {
            return $this->error(Code::ERROR, 'request interface failed');
        }
        $result = (array)$result;
        return isset($result['error_code']) ? $this->error(Code::ERROR, $result['error']) : $result;
    }

    /**
     * TODO:：获取用户信息
     * @param string $access_token
     * @param string $uid
     * @return array
     * @throws Exception
     */
    public function getUserInfo(string $access_token, string $uid): array
    {
        $result = $this->curl->get($this->apiUrl . "2/users/show.json?access_token=$access_token&uid=$uid");
        if (!$result) {
            return $this->error(Code::ERROR, 'request interface failed');
        }
        $result = (array)$result;
        return isset($result['error_code']) ? $this->error(Code::ERROR, $result['error']) : $result;
    }
}

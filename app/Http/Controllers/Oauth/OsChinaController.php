<?php

namespace App\Http\Controllers\Oauth;

use App\Http\Controllers\Utils\Code;

/**
 * Class OsChinaController
 * @author <fl140125@gmail.com>
 * @package App\Http\Controllers\Oauth
 */
class OsChinaController extends OAuthController
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
    protected $apiUrl = 'https://www.oschina.net/';

    /**
     * OsChinaController constructor.
     * @param string $appid
     * @param string $appsecret
     */
    public function __construct(string $appid,string $appsecret)
    {
        parent::__construct();
        $this->appid = $appid;
        $this->appsecret = $appsecret;
        $this->redirectUri = config('app.url').'api/v1/callback/os_china';
    }

    /**
     * @param string $appid
     * @param string $appsecret
     * @return static
     */
    static public function getInstance(string $appid,string $appsecret)
    {
        if (!self::$instance instanceof self) {
            self::$instance = new static($appid,$appsecret);
        }
        return self::$instance;
    }

    /**
     * TODO:：获取github授权页面
     * @param int $length
     * @param string $callback
     * @return string
     */
    public function getAuthUrl($length = 32,$callback = '')
    {
        $arr = [
            'client_id' => $this->appid,
            'response_type' => 'code',
            'redirect_uri' => empty($callback) ? $this->redirectUri : $callback,
            'state' => $this->getState($length),
        ];
        return $this->apiUrl.'action/oauth2/authorize?'.http_build_query($arr);
    }

    /**
     * TODO:：获取access_token
     * @param string $code
     * @return array|bool|mixed
     * @throws \Exception
     */
    public function getAccessToken(string $code)
    {
        $arr = [
            'client_id' => $this->appid,
            'client_secret' => $this->appsecret,
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $this->redirectUri,
            'dataType' => 'json',
            'callback' => 'json'
        ];
        $result = $this->curl->post($this->apiUrl.'action/openapi/token',$arr);
        if (!$result){
            return $this->error(Code::ERROR,'request interface failed');
        }
        $result = object_to_array($result);
        if (isset($result['error'])){
            return $this->error(Code::ERROR,$result['error_description']);
        }
        return $result;
    }

    /**
     * TODO:：刷新AccessToken续期
     * @param string $refreshToken
     * @return array|mixed
     * @throws \Exception
     */
    public function refreshToken(string $refreshToken)
    {
        $arr = [
            'client_id' => $this->appid,
            'client_secret' => $this->appsecret,
            'grant_type' => 'refresh_token',
            'redirect_uri' => $this->redirectUri,
            'refresh_token' => $refreshToken,
            'dataType' => 'json',
            'callback' => 'json'
        ];
        $result = $this->curl->post($this->apiUrl.'action/openapi/token',$arr);
        if (!$result){
            return $this->error(Code::ERROR,'request interface failed');
        }
        $result = object_to_array($result);
        if (isset($result['error'])){
            return $this->error(Code::ERROR,$result['error_description']);
        }
        return $result;
    }

    /**
     * TODO:获取用户信息
     * @param string $access_token
     * @return array|mixed
     */
    public function getUserInfo(string $access_token)
    {
        $arr = [
            'access_token' => $access_token,
            'dataType' => 'json'
        ];
        $result = $this->curl->post($this->apiUrl.'action/openapi/user',$arr);
        if (!$result){
            return $this->error(Code::ERROR,'request interface failed');
        }
        $result = object_to_array($result);
        if (isset($result['error'])){
            return $this->error(Code::ERROR,$result['error_description']);
        }
        return $result;
    }

    /**
     * TODO:获取用户详情
     * @param string $access_token
     * @param string $user
     * @param string $friend
     * @return array|mixed
     */
    public function getUserInformation(string $access_token,string $user,string $friend)
    {
        $arr = [
            'access_token' => $access_token,
            'dataType' => 'json',
            'user' => $user,
            'friend' => $friend
        ];
        $result = $this->curl->post($this->apiUrl.'action/openapi/user_information',$arr);
        if (!$result){
            return $this->error(Code::ERROR,'request interface failed');
        }
        $result = object_to_array($result);
        if (isset($result['error'])){
            return $this->error(Code::ERROR,$result['error_description']);
        }
        return $result;
    }
}

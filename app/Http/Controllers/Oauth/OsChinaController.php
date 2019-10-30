<?php

namespace App\Http\Controllers\Oauth;

use App\Http\Controllers\Utils\Code;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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
     * @param $appid
     * @param $appsecret
     */
    public function __construct($appid,$appsecret)
    {
        parent::__construct();
        $this->appid = $appid;
        $this->appsecret = $appsecret;
        $this->redirectUri = config('app.url').'api/v1/callback/os_china';
    }

    /**
     * @param $appid
     * @param $appsecret
     * @return static
     */
    static public function getInstance($appid,$appsecret)
    {
        if (!self::$instance instanceof self) {
            self::$instance = new static($appid,$appsecret);
        }
        return self::$instance;
    }

    /**
     * todo：获取github授权页面
     * @param string $callback
     * @param int $length
     * @return string
     */
    public function getAuthUrl($callback = '', $length = 32)
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
     * todo：获取access_token
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
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $this->redirectUri,
            'state' => $state,
            'dataType' => 'json',
            'callback' => 'json'
        ];
        $result = $this->curl->post($this->apiUrl.'/action/openapi/token',$arr);
        if (!$result){
            return $this->error(Code::ERROR,'request interface failed');
        }
        $result = json_decode($result,true);
        if (isset($result['error'])){
            return $this->error(Code::ERROR,$result['error_description']);
        }
        return $result;
    }

    /**
     * todo：刷新AccessToken续期
     * @param $refreshToken
     * @return array|mixed
     * @throws \Exception
     */
    public function refreshToken($refreshToken)
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
        $result = $this->curl->post($this->apiUrl.'/action/openapi/token',$arr);
        if (!$result){
            return $this->error(Code::ERROR,'request interface failed');
        }
        $result = json_decode($result,true);
        if (isset($result['error'])){
            return $this->error(Code::ERROR,$result['error_description']);
        }
        return $result;
    }
}

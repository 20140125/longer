<?php

namespace App\Http\Controllers\Oauth;

use App\Http\Controllers\Utils\Code;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

/**
 * Class QQController
 * @author <fl140125@gmail.com>
 * @package App\Http\Controllers\Oauth
 */
class QQController extends OAuthController
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
     * @var string API 业务域名
     */
    protected $apiUrl = 'https://graph.qq.com/';
    /**
     * @var string 授权成功回调地址
     */
    protected $redirectUri;
    /**
     * @var string $openid
     */
    public $openid;
    /**
     * @var static $instance
     */
    protected static $instance;

    /**
     * QQController constructor.
     * @param string $appid
     * @param string $appsecret
     */
    public function __construct(string $appid, string $appsecret)
    {
        parent::__construct();
        $this->appid = $appid;
        $this->appsecret = $appsecret;
        $this->redirectUri = config('app.url').'api/v1/callback/qq';
    }

    /**
     * @param string $appid
     * @param string $appsecret
     * @return QQController
     */
    public static function getInstance(string $appid, string $appsecret)
    {
        if (!self::$instance instanceof self) {
            self::$instance = new static($appid, $appsecret);
        }
        return self::$instance;
    }

    /**
     * TODO:：获取登录页面跳转转URL
     * @param int $length
     * @param string $callback
     * @param string $scope
     * @return string
     */
    public function getAuthUrl($length = 32, $callback = '', $scope = 'get_user_info')
    {
        $arr = [
            'response_type' => 'code',
            'client_id' => $this->appid,
            'redirect_uri' => empty($callback)?$this->redirectUri:$callback,
            'state' => $this->getState($length),
            'scope' => $scope,
            'display' => ''
        ];
        return $this->apiUrl.'oauth2.0/authorize?'.http_build_query($arr);
    }

    /**
     * TODO:：获取access_token
     * @param string $code
     * @return array|bool
     * @throws \Exception
     */
    public function getAccessToken(string $code)
    {
        $arr = [
            'grant_type' => 'authorization_code',
            'client_id' => $this->appid,
            'client_secret' => $this->appsecret,
            'code' => $code,
            'redirect_uri' => $this->redirectUri
        ];
        $result = $this->curl->get($this->apiUrl.'oauth2.0/token?'.http_build_query($arr));
        if (!$result) {
            return $this->error(Code::ERROR, 'request interface failed');
        }
        if (isset($this->json($result)['error'])) {
            return $this->error(Code::ERROR, $this->json($result)['error_description']);
        }
        return  $this->__getAccessToken($result);
    }

    /**
     * TODO:：获取用户的openid
     * @param string $access_token
     * @return array
     * @throws \Exception
     */
    public function getOpenId(string $access_token)
    {
        $arr = [
            'access_token' =>$access_token
        ];
        $result = $this->curl->get($this->apiUrl.'oauth2.0/me?'.http_build_query($arr));
        if (!$result) {
            return $this->error(Code::ERROR, 'request interface failed');
        }
        if (isset($this->json($result)['error'])) {
            return $this->error(Code::ERROR, $this->json($result)['error_description']);
        }
        return $this->json($result)['openid'];
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
            'grant_type' => 'refresh_token',
            'client_id' => $this->appid,
            'client_secret' => $this->appsecret,
            'refresh_token' => $refreshToken
        ];
        $result = $this->curl->get($this->apiUrl.'oauth2.0/token?'.http_build_query($arr));
        if (!$result) {
            return $this->error(Code::ERROR, 'request interface failed');
        }
        if (isset($this->json($result)['error'])) {
            return $this->error(Code::ERROR, $this->json($result)['error_description']);
        }
        return  $this->__getAccessToken($result);
    }

    /**
     * TODO:：获取用户信息
     * @param string $access_token
     * @return JsonResponse|mixed
     * @throws \Exception
     */
    public function getUserInfo(string $access_token)
    {
        $this->openid = $this->getOpenId($access_token);
        $arr = [
            'access_token' => $access_token,
            'oauth_consumer_key' => $this->appid,
            'openid' => $this->openid
        ];
        $result = $this->curl->get($this->apiUrl.'user/get_user_info?'.http_build_query($arr));
        if (!$result) {
            return $this->error(Code::ERROR, 'request interface failed');
        }
        $result = json_decode($result, true);
        if (isset($result['ret']) && $result['ret']!=0) {
            return $this->error(Code::ERROR, $result['msg']);
        }
        return $result;
    }
}

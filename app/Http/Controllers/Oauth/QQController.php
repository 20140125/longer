<?php

namespace App\Http\Controllers\Oauth;

use App\Http\Controllers\Utils\Code;
use Illuminate\Http\JsonResponse;

/**
 * Class QQController
 * @author <fl140125@gmail.com>
 * @package App\Http\Controllers\Oauth
 */
class QQController extends OAuthController
{
    /**
     * @var $appid
     */
    protected $appid;
    /**
     * @var $appsecret
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
     * @param $appid
     * @param $appsecret
     */
    public function __construct($appid,$appsecret)
    {
        parent::__construct();
        $this->appid = $appid;
        $this->appsecret = $appsecret;
        $this->redirectUri = config('app.url').'api/v1/callback/qq';
    }

    /**
     * @param $appid
     * @param $appsecret
     * @return QQController
     */
    static public function getInstance($appid,$appsecret)
    {
        if (!self::$instance instanceof self){
            self::$instance = new static($appid,$appsecret);
        }
        return self::$instance;
    }

    /**
     * todo：获取登录页面跳转转URL
     * @param string $callback
     * @param int $length
     * @param string $scope
     * @return mixed
     */
    public function getAuthUrl($callback = '',$length = 16,$scope = 'get_user_info')
    {
        $arr = [
            'response_type' => 'code',
            'client_id' => $this->appid,
            'redirect_uri' => empty($callback)?$this->redirectUri:$callback,
            'state' => $this->getState($length),
            'scope' => $scope,
            'display' => ''
        ];
        $result = $this->apiUrl.'oauth2.0/authorize?'.http_build_query($arr);
        return $result;
    }

    /**
     * todo：获取access_token
     * @param $code
     * @return array|bool
     * @throws \Exception
     */
    public function getAccessToken($code)
    {
        $arr = [
            'grant_type' => 'authorization_code',
            'client_id' => $this->appid,
            'client_secret' => $this->appsecret,
            'code' => $code,
            'redirect_uri' => $this->redirectUri
        ];
        $result = $this->curl->get($this->apiUrl.'oauth2.0/token?'.http_build_query($arr));
        if (!$result){
            return $this->error(Code::ERROR,'接口请求失败');
        }
        $result = $this->__getAccessToken($result);
        if (isset($result['access_token'])){
            return $result;
        }
        return false;
    }

    /**
     * todo：获取用户的openid
     * @param $access_token
     * @return array
     * @throws \Exception
     */
    public function getOpenId($access_token)
    {
        $arr = [
            'access_token' =>$access_token
        ];
        $result = $this->curl->get($this->apiUrl.'oauth2.0/me?'.http_build_query($arr));
        if (!$result){
            return $this->error(Code::ERROR,'接口请求失败');
        }
        if (isset($this->json($result)['error'])){
            return $this->error(Code::ERROR,$this->json($result)['error_description']);
        }
        return $this->json($result)['openid'];
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
            'grant_type'	=>	'refresh_token',
            'client_id'		=>	$this->appid,
            'client_secret'	=>	$this->appsecret,
            'refresh_token'	=>	$refreshToken
        ];
        $result = $this->curl->get($this->apiUrl.'oauth2.0/token?'.http_build_query($arr));
        if (!$result){
            return $this->error(Code::ERROR,'接口请求失败');
        }
        if (isset($this->json($result)['error'])){
            return $this->error(Code::ERROR,$this->json($result)['error_description']);
        }
        return  $this->__getAccessToken($result);
    }

    /**
     * todo：获取用户信息
     * @param $access_token
     * @return JsonResponse|mixed
     * @throws \Exception
     */
    public function getUserInfo($access_token)
    {
        $this->openid = $this->getOpenId($access_token);
        $arr = [
            'access_token' => $access_token,
            'oauth_consumer_key' => $this->appid,
            'openid' => $this->openid
        ];
        $result = $this->curl->get($this->apiUrl.'user/get_user_info?'.http_build_query($arr));
        if (!$result){
            return $this->error(Code::ERROR,'接口请求失败');
        }
        $result = json_decode($result,true);
        if (isset($result['ret']) && $result['ret']!=0){
            return ajax_return(Code::ERROR,$result['msg']);
        }
        return $result;
    }
}

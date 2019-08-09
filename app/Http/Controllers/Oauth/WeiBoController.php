<?php

namespace App\Http\Controllers\Oauth;
use App\Http\Controllers\Utils\Code;

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
     * @var string $appsecret
     */
    protected $appsecret;
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
     * @param $appid
     * @param $appsecret
     */
    public function __construct($appid,$appsecret)
    {
        parent::__construct();
        $this->appid = $appid;
        $this->appsecret = $appsecret;
        $this->redirectUri = config('app.url').'api/v1/callback/weibo';
    }

    /**
     * @param $appid
     * @param $appsecret
     * @return WeiBoController
     */
    static public function getInstance($appid,$appsecret)
    {
        if (!self::$instance instanceof self) {
            self::$instance = new static($appid,$appsecret);
        }
        return self::$instance;
    }

    /**
     * todo：获取授权登录URL
     * @param string $callback
     * @param int $length
     * @param string $scope
     * @return string
     */
    public function getAuthUrl($callback = '',$length = 32,$scope = 'all,email')
    {
        $this->redirectUri = empty($callback) ? $this->redirectUri :$callback;
        $arr = [
            'client_id' => $this->appid,
            'redirect_uri' =>$this->redirectUri,
            'scope' => $scope,
            'state' => $this->getState($length),
            'display' => 'default',  // default mobile wap client
            'forcelogin' => false, //是否强制用户重新登录，true：是，false：否。默认false。
            //'language' =>''  //授权页语言，缺省为中文简体版，en为英文版
        ];
        return $this->apiUrl.'oauth2/authorize?'.http_build_query($arr);
    }

    /**
     * todo：获取access_token
     * @param $code
     * @return mixed
     * @throws \Exception
     */
    public function getAccessToken($code)
    {
        $arr = [
            'client_id' => $this->appid,
            'client_secret' => $this->appsecret,
            'grant_type' => 'authorization_code',
            'code' => $code,
            'redirect_uri' => $this->redirectUri
        ];
        $result = $this->curl->post($this->apiUrl.'oauth2/access_token',$arr);
        if (!$result){
            return $this->error(Code::ERROR,'request interface failed');
        }
        $result = object_to_array($result);
        if (isset($result['error_code'])){
            return $this->error(Code::ERROR,$result['error']);
        }
        return $result;
    }

    /**
     * todo：获取用户信息
     * @param $access_token
     * @param $uid
     * @return array|mixed
     * @throws \Exception
     */
    public function getUserInfo($access_token,$uid)
    {
        $result = $this->curl->get($this->apiUrl."2/users/show.json?access_token={$access_token}&uid={$uid}");
        if (!$result){
            return $this->error(Code::ERROR,'request interface failed');
        }
        $result = object_to_array($result);
        if (isset($result['error_code'])){
            return $this->error(Code::ERROR,$result['error']);
        }
        return $result;
    }
}

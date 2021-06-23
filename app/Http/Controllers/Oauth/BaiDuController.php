<?php

namespace App\Http\Controllers\Oauth;

use App\Http\Controllers\Utils\Code;

/**
 * Class BaiDuController
 * @author <fl140125@gmail.com>
 * @package App\Http\Controllers\Oauth
 */
class BaiDuController extends OAuthController
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
    protected $apiUrl = 'https://openapi.baidu.com/';
    /**
     * @var string 授权成功回调地址
     */
    protected $redirectUri;
    /**
     * @var static $instance
     */
    protected static $instance;

    /**
     * BaiDu constructor.
     * @param string $appid
     * @param string $appsecret
     */
    public function __construct(string $appid, string $appsecret)
    {
        parent::__construct();
        $this->appid = $appid;
        $this->appsecret = $appsecret;
        $this->redirectUri = config('app.url').'api/v1/callback/baidu';
    }
    /**
     * @param string $appid
     * @param string $appsecret
     * @return BaiDuController
     */
    public static function getInstance(string $appid, string $appsecret)
    {
        if (!self::$instance instanceof self) {
            self::$instance = new static($appid, $appsecret);
        }
        return self::$instance;
    }

    /**
     * TODO：获取授权地址
     * @param string $callback
     * @param int $length
     * @return string
     */
    public function getAuthUrl($length = 32, $callback = '')
    {
        /**
         * display desc
         * page：全屏形式的授权页面(默认)，适用于web应用。
         * popup: 弹框形式的授权页面，适用于桌面软件应用和web应用。
         * dialog:浮层形式的授权页面，只能用于站内web应用。
         * mobile: IPhone/Android等智能移动终端上用的授权页面，适用于IPhone/Android等智能移动终端上的应用。
         * pad: IPad/Android等平板上使用的授权页面，适用于IPad/Android等智能移动终端上的应用。
         * tv: 电视等超大显示屏使用的授权页面。
         */
        $arr = [
            'response_type' =>'code',
            'client_id' => $this->appid,
            'redirect_uri' => empty($callback) ? $this->redirectUri : $callback,
            'scope' => 'basic super_msg',
            'state' => $this->getState($length),
            'display' => 'dialog',
            'force_login' => '0', //如传递“force_login=1”，则加载登录页时强制用户输入用户名和口令，不会从cookie中读取百度用户的登陆状态。
            'confirm_login' =>'1', // 如传递“confirm_login=1”且百度用户已处于登陆状态，会提示是否使用已当前登陆用户对应用授权。
            'login_type' => 'sms' //如传递“login_type=sms”，授权页面会默认使用短信动态密码注册登陆方式。
        ];
        return $this->apiUrl.'oauth/2.0/authorize?'.http_build_query($arr);
    }

    /**
     * TODO：获取access_token
     * @param string $code
     * @return array|mixed
     */
    public function getAccessToken(string $code)
    {
        $arr = [
            'grant_type' => 'authorization_code',
            'code' => $code,
            'client_id' => $this->appid,
            'client_secret' => $this->appsecret,
            'redirect_uri' => $this->redirectUri,
        ];
        $result = $this->curl->get($this->apiUrl.'oauth/2.0/token?'.http_build_query($arr));
        if (!$result) {
            return $this->error(Code::ERROR, 'request interface failed');
        }
        $result = (array)$result;
        return isset($result['error']) ? $this->error(Code::ERROR, $result['error_description']) : $result;
    }

    /**
     * TODO:获取用户信息（待废弃）
     * @param string $access_token
     * @return array|mixed
     */
    public function getUserInfo2(string $access_token)
    {
        $arr = [
            'access_token' => $access_token,
            'format' => 'json'
        ];
        $result = $this->curl->get($this->apiUrl.'rest/2.0/passport/users/getLoggedInUser?'.http_build_query($arr));
        if (!$result) {
            return $this->error(Code::ERROR, 'request interface failed');
        }
        $result = (array)$result;
        return isset($result['error']) ? $this->error(Code::ERROR, $result['error_description']) : $result;
    }

    /**
     * TODO:获取用户信息
     * @param string $access_token
     * @return array|bool|mixed
     */
    public function getUserInfo(string $access_token)
    {
        $arr = [
            'access_token' => $access_token,
            'format' => 'json',
            'get_unionid' => '1',
        ];
        $result = $this->curl->get($this->apiUrl.'rest/2.0/passport/users/getInfo?'.http_build_query($arr));
        if (!$result) {
            return $this->error(Code::ERROR, 'request interface failed');
        }
        $result = (array)$result;
        return isset($result['error']) ? $this->error(Code::ERROR, $result['error_description']) : $result;
    }

    /**
     * TODO：刷新AccessToken
     * @param string $refreshToken
     * @return array|mixed
     */
    public function refreshToken(string  $refreshToken)
    {
        $arr =  array(
            'grant_type' => 'refresh_token',
            'refresh_token' => $refreshToken,
            'client_id' => $this->appid,
            'client_secret' => $this->appsecret,
            'scope' => 'basic'
        );
        $result = $this->curl->get($this->apiUrl.'oauth/2.0/token?'.http_build_query($arr));
        if (!$result) {
            return $this->error(Code::ERROR, 'request interface failed');
        }
        $result = (array)$result;
        return isset($result['error']) ? $this->error(Code::ERROR, $result['error_description']) : $result;
    }
}

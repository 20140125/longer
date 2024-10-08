<?php

namespace App\Http\Controllers\Oauth;

use App\Http\Controllers\Utils\Code;
use Exception;

/**
 * Class GiteeController
 * @author <fl140125@gmail.com>
 * @package App\Http\Controllers\Oauth
 */
class GiteeController extends OAuthController
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
     * @var string API 业务域名
     */
    protected $apiUrl = 'https://gitee.com/';
    /**
     * @var static $instance
     */
    protected static $instance;

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
        $this->redirectUri = config('app.url') . 'api/v1/callback/gitee';
    }

    /**
     * @param string $appid
     * @param string $appsecret
     * @return GiteeController
     */
    public static function getInstance(string $appid, string $appsecret)
    {
        if (!self::$instance instanceof self) {
            self::$instance = new static($appid, $appsecret);
        }
        return self::$instance;
    }

    /**
     * 获取登录页面跳转url
     * @param int $length
     * @param string $callback
     * @return string
     */
    public function getAuthUrl(int $length = 32, string $callback = '')
    {
        $arr = array(
            'client_id'     => $this->appid,
            'redirect_uri'  => empty($callback) ? $this->redirectUri : $callback,
            'response_type' => 'code',
            'state'         => $this->getState($length)
        );
        return $this->apiUrl . 'oauth/authorize?' . http_build_query($arr);
    }

    /**
     * 获取access_token
     * @param string $code
     * @return array
     * @throws Exception
     */
    public function getAccessToken(string $code)
    {
        $arr = [
            'grant_type'    => 'authorization_code',
            'code'          => $code,
            'client_id'     => $this->appid,
            'redirect_uri'  => $this->redirectUri,
            'client_secret' => $this->appsecret
        ];
        $result = $this->curl->post($this->apiUrl . "oauth/token?" . http_build_query($arr));
        if (!$result) {
            return $this->error(Code::ERROR, 'request interface failed');
        }
        $result = (array)$result;
        return isset($result['error']) ? $this->error(Code::ERROR, $result['error_description']) : $result;
    }

    /**
     * 刷新AccessToken续期
     * @param string $refresh_token
     * @return array
     * @throws Exception
     */
    public function refreshToken(string $refresh_token)
    {
        $arr = [
            'grant_type'    => 'refresh_token',
            'refresh_token' => $refresh_token
        ];
        $result = $this->curl->post($this->apiUrl . "oauth/token?" . http_build_query($arr));
        if (!$result) {
            return $this->error(Code::ERROR, 'request interface failed');
        }
        $result = (array)$result;
        return isset($result['error']) ? $this->error(Code::ERROR, $result['error_description']) : $result;
    }

    /**
     * 获取用户资料
     * @param string $access_token
     * @return array
     * @throws Exception
     */
    public function getUserInfo(string $access_token)
    {
        $result = $this->curl->get($this->apiUrl . "api/v5/user?access_token=$access_token");
        if (!$result) {
            return $this->error(Code::ERROR, 'request interface failed');
        }
        $result = (array)$result;
        return isset($result['error']) ? $this->error(Code::ERROR, $result['error_description']) : $result;
    }
}

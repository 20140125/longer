<?php

namespace App\Http\Controllers\Oauth;

use App\Http\Controllers\Utils\Code;

/**
 * Class GiteeController
 * @author <fl140125@gmail.com>
 * @package App\Http\Controllers\Oauth
 */
class GiteeController extends OauthController
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
     * GithubController constructor.
     * @param $appid
     * @param $appsecret
     */
    public function __construct($appid,$appsecret)
    {
        parent::__construct();
        $this->appid = $appid;
        $this->appsecret = $appsecret;
        $this->redirectUri = config('app.url').'/api/v1/callback/gitee';
    }

    /**
     * todo：获取登录页面跳转url
     * @param string $callback
     * @param int $length
     * @return string
     */
    public function getAuthUrl($callback = '', $length = 32)
    {
        $arr = array(
            'client_id'			=>	$this->appid,
            'redirect_uri' => empty($callback) ? $this->redirectUri : $callback,
            'response_type'		=>	'code',
            'state'				=>	$this->getState($length),
        );
        return $this->apiUrl.'oauth/authorize?'.http_build_query($arr);
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
            'grant_type'	=>	'authorization_code',
            'code'			=>	$code,
            'client_id'		=>	$this->appid,
            'redirect_uri'	=>	$this->redirectUri,
            'client_secret'	=>	$this->appsecret
        ];
        $this->curl->setHeader("User-Agent", "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.142 Safari/537.36");
        $result = $this->curl->post($this->apiUrl."oauth/token?".http_build_query($arr));
        if (!$result){
            return $this->error(Code::ERROR,'接口请求失败');
        }
        return $result;
    }

    /**
     * todo：获取用户资料
     * @param $access_token
     * @return mixed
     * @throws \Exception
     */
    public function getUserInfo($access_token)
    {
        $result = $this->curl->get($this->apiUrl."api/v5/user?access_token=$access_token");
        if (!$result){
            return $this->error(Code::ERROR,'接口请求失败');
        }
        return $result;
    }
}

<?php

namespace App\Http\Controllers\Oauth;


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
        $this->appid = $appid;
        $this->appsecret = $appsecret;
        $this->redirectUri = config('app.url').'/api/v1/callback/gitee';
    }

    /**
     * todo：获取登录页面跳转url
     * @param string $callbackUrl
     * @param int $length
     * @return string
     */
    public function getAuthUrl($callbackUrl = '', $length = 32)
    {
        $arr = array(
            'client_id'			=>	$this->appid,
            'redirect_uri'		=>	null === $callbackUrl ? $this->redirectUri : $callbackUrl,
            'response_type'		=>	'code',
            'state'				=>	$this->getState($length),
        );
        return $this->apiUrl.'oauth/authorize?'.http_build_query($arr);
    }

    /**
     * todo：获取access_token
     * @param $code
     * @param $state
     * @return mixed
     * @throws \Exception
     */
    protected function getAccessToken($code,$state)
    {
        $arr = [
            'grant_type'	=>	'authorization_code',
            'code'			=>	$code,
            'state'         =>  $state,
            'client_id'		=>	$this->appid,
            'redirect_uri'	=>	$this->redirectUri,
            'client_secret'	=>	$this->appsecret,
        ];
        $result = http_query($this->apiUrl.'oauth/token',$arr);
        if ($result['state'] != 200) {
            throw new \Exception('网络异常');
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
        $result = http_query($this->apiUrl.'api/v5/user?'.'access_token='.$access_token);
        if ($result['state'] != 200) {
            throw new \Exception('网络异常');
        }
        return $result['data'];
    }
}

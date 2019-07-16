<?php

namespace App\Http\Controllers\Oauth;

use App\Http\Controllers\Utils\Code;
use http\Exception;
use Illuminate\Http\JsonResponse;

/**
 * Class GithubController
 * @package App\Http\Controllers\Oauth
 */
class GithubController extends OauthController
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
    protected $apiUrl = 'https://github.com/';

    /**
     * GithubController constructor.
     * @param $appid
     * @param $appsecret
     */
    public function __construct($appid,$appsecret)
    {
        $this->appid = $appid;
        $this->appsecret = $appsecret;
        $this->redirectUri = config('app.url').'/api/v1/callback/github';
    }

    /**
     * todo：获取github授权页面
     * @param string $callback
     * @param int $length
     * @param string $scope
     * @return string
     */
    public function getAuthUrl($callback = '', $length = 32, $scope = 'user:email')
    {
        $arr = [
            'client_id' => $this->appid,
            'redirect_uri' => empty($callback) ? $this->redirectUri : $callback,
            'scope' => $scope,  // user:email read:user user:follow
            'state' => $this->getState($length),
            'allow_signup' => false, //是否在登录页显示注册，默认false
        ];
        return $this->apiUrl.'login/oauth/authorize?'.http_build_query($arr);
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
            'code' => $code,
            'redirect_uri' => $this->redirectUri,
            'state' => $state
        ];
        $result = http_query($this->apiUrl.'login/oauth/access_token',$arr);
        if ($result['state'] != 200) {
            throw new \Exception('网络异常');
        }
        $result = $this->__getAccessToken($result['data']);
        if (isset($result['access_token'])){
            return $result;
        }
        return false;

    }

    /**
     * todo：获取用户信息
     * @param $access_token
     * @return mixed
     * @throws \Exception
     */
    public function getUserInfo($access_token)
    {
        $result = http_query('https://api.github.com/user?access_token='.$access_token);
        if ($result['state'] != 200) {
            throw new \Exception('网络异常');
        }
        return $result['data'];
    }
}

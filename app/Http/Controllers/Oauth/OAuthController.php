<?php

namespace App\Http\Controllers\Oauth;

use Curl\Curl;
use App\Http\Controllers\Controller;

/**
 * Class OauthController
 * @author <fl140125@gmail.com>
 * @package App\Http\Controllers\Oauth
 */
class OAuthController extends Controller
{
    /**
     * @var string $state
     */
    public string $state;
    /**
     * @var Curl $curl
     */
    protected Curl $curl;

    public function __construct()
    {
        $this->curl = new Curl();
        $this->curl->setHeader(
            "User-Agent",
            "Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/75.0.3770.142 Safari/537.36"
        );
    }

    /**
     * 获取状态值
     * @param int $length
     * @return string
     */
    public function getState(int $length = 32): string
    {
        $this->state = substr(encrypt(getRoundNum($length, 'all')), 0, $length);
        return $this->state;
    }

    /**
     * 数据转换成数组格式
     * @param $data
     * @return mixed
     */
    public function json($data)
    {
        return json_decode(str_replace(['callback', '(', ')', ';'], '', $data), true);
    }

    /**
     * 获取错误信息
     * @param $code
     * @param $message
     * @return array
     */
    public function error($code, $message): array
    {
        return array('code' => $code, 'message' => $message);
    }

    /**
     * @param $data
     * @return array
     */
    public function __getAccessToken($data): array
    {
        $queryParts = explode('&', $data);
        $paramsArr = array();
        foreach ($queryParts as $param) {
            $item = explode('=', $param);
            if (is_array($item) && count($item) > 0) {
                $paramsArr[$item[0]] = $item[1];
            }
        }
        return $paramsArr;
    }
}

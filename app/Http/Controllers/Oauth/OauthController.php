<?php

namespace App\Http\Controllers\Oauth;

use Curl\Curl;
use App\Http\Controllers\Controller;

/**
 * Class OauthController
 * @author <fl140125@gmail.com>
 * @package App\Http\Controllers\Oauth
 */
class OauthController extends Controller
{
    public $state;
    /**
     * @var Curl
     */
    protected $curl;

    public function __construct()
    {
        $this->curl = new Curl();
    }

    /**
     * todo：获取状态值
     * @param int $length
     * @return string
     */
    public function getState($length = 8)
    {
        $this->state = md5(get_round_num($length));
        return $this->state;
    }
    /**
     * todo：校验标识是否正确
     * @param $storeState
     * @param $state
     * @return bool
     */
    public function checkState($storeState,$state)
    {
        return $storeState === $state ? true : false;
    }

    /**
     * todo：数据转换成数组格式
     * @param $data
     * @return mixed
     */
    public function json($data)
    {
        return json_decode(str_replace(['callback','(',')',';'],'',$data),true);
    }

    /**
     * todo：获取错误信息
     * @param $code
     * @param $message
     * @return array
     */
    public function error($code,$message)
    {
        return array('code'=>$code,'message'=>$message);
    }

    /**
     * @param $data
     * @return array|bool
     */
    public function __getAccessToken($data)
    {
        $queryParts = explode('&', $data);
        $paramsArr = array();
        foreach ($queryParts as $param) {
            $item = explode('=', $param);
            if (is_array($item) && !empty($item)){
                $paramsArr[$item[0]] = $item[1];
            }
        }
        return $paramsArr;
    }
}

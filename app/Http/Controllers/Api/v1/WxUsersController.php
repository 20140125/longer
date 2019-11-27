<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;
use App\Http\Controllers\Utils\crypt\WXBizDataCrypt;
use App\Models\OAuth;
use Curl\Curl;
use Illuminate\Config\Repository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


/**
 * TODO: 用户管理
 * Class UsersController
 * @author <fl140125@gmail.com>
 * @package App\Http\Controllers\Api\v1
 */
class WxUsersController
{
    protected $post;
    /**
     * @var Repository|mixed 用户APPID（小程序）
     */
    protected $appid;
    /**
     * @var Repository|mixed 用户密钥（小程序）
     */
    protected $appsecret;

    /**
     * WxUsersController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->appid = config('app.program_appid');
        $this->appsecret = config('app.program_secret');
        $this->post = $request->post();
    }

    /**
     * TODO: 微信获取用户的openid
     * @param string code 微信code
     * @return JsonResponse
     * @throws \ErrorException
     */
    public function getOpenId()
    {
        $this->validatePost(['code'=>'required|string']);
        $url = 'https://api.weixin.qq.com/sns/jscode2session?';
        $data = array(
            'appid' =>$this->appid,
            'secret' =>$this->appsecret,
            'js_code' =>$this->post['code'],
            'grant_type' =>'authorization_code'
        );
        $curl = new Curl();
        $response = $curl->post($url.http_build_query($data));
        $parsedData = json_decode(trim($response), true, 512, JSON_OBJECT_AS_ARRAY);
        return $this->ajax_return(Code::SUCCESS,'successfully',$parsedData);
    }

    /**
     * TODO: 微信登陆信息
     * @return JsonResponse
     */
    public function login()
    {
        return $this->ajax_return(Code::SUCCESS,'login successfully');
    }

    /**
     * TODO:数据返回
     * @param $code
     * @param $msg
     * @param array $data
     * @return JsonResponse
     */
    protected function ajax_return($code,$msg,$data = array())
    {
        $item = array(
            'code' =>$code,
            'msg' =>$msg,
            'item' =>$data,
        );
        return response()->json($item,$code);
    }

    /**
     * TODO：数据检验
     * @param array $rules
     * @param array $message
     */
    protected function validatePost(array $rules,array $message = [])
    {
        $validate = Validator::make($this->post,$rules,$message);
        if ($validate->fails()) {
            if ($validate->errors()->first() == 'Permission denied'){
                $this->setCode(Code::NOT_ALLOW,$validate->errors()->first());
            }
            $this->setCode(Code::ERROR,$validate->errors()->first());
        }
    }
    /**
     * TODO:设置code
     * @param $code
     * @param $message
     */
    protected function setCode($code,$message)
    {
        set_code($code);
        exit(json_encode(array('code'=>$code,'msg'=>$message)));
    }
}

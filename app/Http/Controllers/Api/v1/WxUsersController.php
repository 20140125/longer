<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;
use Curl\Curl;
use Illuminate\Config\Repository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

/**
 * TODO: 用户管理
 * Class WxUsersController
 * @author <fl140125@gmail.com>
 * @package App\Http\Controllers\Api\v1
 */
class WxUsersController
{
    protected array|string|null $post;
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
        if ($request->isMethod('get')) {
            $this->setCode(Code::METHOD_ERROR, 'method not allow');
        }
        $this->appid = config('app.program_appid');
        $this->appsecret = config('app.program_secret');
        $this->post = $request->post();
    }

    /**
     * TODO: 微信获取用户的openid
     * @param string code 微信code
     * @return JsonResponse
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
        return $this->ajaxReturn(Code::SUCCESS, 'successfully', $parsedData);
    }

    /**
     * TODO: 微信登陆信息
     * @return JsonResponse
     */
    public function login()
    {
        return $this->ajaxReturn(Code::SUCCESS, 'login successfully');
    }

    /**
     * TODO:数据返回
     * @param $code
     * @param $msg
     * @param array $data
     * @return JsonResponse
     */
    protected function ajaxReturn($code, $msg, $data = array())
    {
        $item = array(
            'code' =>$code,
            'msg' =>$msg,
            'item' =>$data,
        );
        return response()->json($item, $code);
    }

    /**
     * TODO：数据检验
     * @param array $rules
     * @param array $message
     */
    protected function validatePost(array $rules, array $message = [])
    {
        $validate = Validator::make($this->post, $rules, $message);
        if ($validate->fails()) {
            if ($validate->errors()->first() == 'Permission denied') {
                $this->setCode(Code::NOT_ALLOW, $validate->errors()->first());
            }
            $this->setCode(Code::ERROR, $validate->errors()->first());
        }
    }
    /**
     * TODO:设置code
     * @param $code
     * @param $message
     */
    protected function setCode($code, $message)
    {
        setCode($code);
        exit(json_encode(array('code'=>$code,'msg'=>$message)));
    }

    /**
     * TODO:：文件上传
     * @param Request $request (file:文件资源,rand:是否随机,path:文件路径)
     * @return JsonResponse
     */
    public function upload(Request $request)
    {
        $file = $request->file('file');
        if ($file->isValid()) {
            //获取文件的扩展名
            $ext = $file->getClientOriginalExtension();
            //获取文件的绝对路径
            $path = $file->getRealPath();
            //图片格式上传错误
            switch (strtolower($ext)) {
                case 'jpg':
                case 'gif':
                case 'png':
                case 'jpeg':
                    //图片大小上传错误
                    if ($file->getSize()>2*1024*1024) {
                        return $this->ajaxReturn(Code::ERROR, 'upload image size error');
                    }
                    break;
                case 'mp4':
                    if ($file->getSize()>5*1024*1024) {
                        return $this->ajaxReturn(Code::ERROR, 'upload video size error');
                    }
                    break;
                default:
                    return $this->ajaxReturn(Code::ERROR, 'Unsupported file format');
            }
            $filename = date('Ymd')."/".md5(date('YmdHis')).uniqid().".".$ext;
            Storage::disk('public')->put($filename, file_get_contents($path));
            $info = array(
                'username' => $this->post['username'] ?? 'tourist',
                'href' => '/v1/wx/upload',
                'msg' => 'upload file '.$file->getClientOriginalName().' successfully'
            );
            actLog($info);
            return $this->ajaxReturn(
                Code::SUCCESS,
                'upload file '.$file->getClientOriginalName().' successfully',
                array('src'=>config('app.url').'storage/'.$filename)
            );
        }
        return $this->ajaxReturn(Code::ERROR, 'upload file failed');
    }
}

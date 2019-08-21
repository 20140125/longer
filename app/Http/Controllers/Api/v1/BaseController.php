<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;
use App\Http\Controllers\Utils\Rsa;
use App\Http\Controllers\Controller;
use App\Models\OAuth;
use App\Models\Role;
use App\Models\Auth;
use App\Models\Users;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * TODO:公共类
 * Class BaseController
 * @author <fl140125@gmail.com>
 * @package App\Http\Controllers\Api\v1
 */
class BaseController extends Controller
{
    /**
     * @var Users $userModel 用户模型
     */
    protected $userModel;
    /**
     * @var Role $roleModel 角色模型
     */
    protected $roleModel;
    /**
     * @var Auth $authModel
     */
    protected $authModel;
    /**
     * @var OAuth $oauthModel
     */
    protected $oauthModel;
    /**
     * @var array $post 请求数据
     */
    protected $post;
    /**
     * @var bool|Model|Builder|object|null 用户信息
     */
    protected $users;
    /**
     * @var Model|Builder|object|null 角色信息
     */
    protected $role;
    /**
     * @var Rsa $rsa rsa 加密
     */
    protected $rsaUtils;
    /**
     * @var string $backupPath 备份地址
     */
    protected $backupPath;

    /**
     * TODO:构造函数
     * BaseController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $url = $request->getRequestUri();
        if (strstr($url,'?')){
            $url = substr($url,0,find_str($request->getRequestUri(),'?',2));
        }
        if ($request->isMethod('get') && asset($url)!==route('downloadFile')){
            $this->setCode(Code::METHOD_ERROR,'Method Not Allowed');
        }
        $this->post = $request->post();
        $this->userModel = Users::getInstance();
        $this->roleModel = Role::getInstance();
        $this->authModel = Auth::getInstance();
        $this->oauthModel = OAuth::getInstance();
        $this->backupPath = base_path('database/migrations');
        //公用权限
        $common_url = [
            route('checkLogin'),
            route('apiLogout'),
            route('logSave'),
            route('menu'),
            route('reqRuleSave'),
        ];
        //判断必填字段是否为空
        $validate = Validator::make($this->post,['token'=>'required|string|size:32']);
        //token不正确用户未授权登录
        if ($validate->fails() || empty($request->header('Authorization'))) {
            $this->setCode(Code::Unauthorized,'user login state expired');
        }
        //用户不存在
        $this->users = $this->userModel->getResult('remember_token',$this->post['token']) ?? $this->oauthModel->getResult('remember_token',$this->post['token']);
        if (empty($this->users) || !in_array($this->post['token'],explode('-',$request->header('Authorization')))) {
            $this->setCode(Code::Unauthorized,'user does not exists');
        }
        //用户被禁用
        if ($this->users->status!==1){
            $this->setCode(Code::Unauthorized,'user disabled');
        }
        //用户不属于超级管理员
        $this->role = $this->roleModel->getResult('id',$this->users->role_id,'=',['auth_url','auth_ids']);
        if ($this->users->role_id !== 1) {
            if (!empty($this->role) && !in_array(str_replace(['/api/v1'],['/admin'],$url),json_decode($this->role->auth_url,true)) && !in_array(asset($url),$common_url)) {
                $this->setCode(Code::NOT_ALLOW,'Permission denied');
            }
        }
        if (!in_array(asset($url),$common_url)) {
            unset($this->post['token']);
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
     * TODO:公钥加密
     * @param string $data
     * @return string|null
     */
    protected function publicEncrypt($data='')
    {
        return $this->rsaUtils::publicEncrypt($data);
    }

    /**
     * TODO:私钥解密
     * @param string $data
     * @return null
     */
    protected function privateDecrypt($data='')
    {
        return $this->rsaUtils::privateDecrypt($data);
    }

    /**
     * TODO：站内信息推送
     * @param $content
     * @param string $uid
     * @return bool|string
     */
    protected function workerManPush($content,$uid='')
    {
        // 推送的url地址
        $push_api_url = config('app.push_url');
        $post_data = array(
            "type" => "publish",
            "content" => $content,
            "to" => empty($uid) ? '' : md5($uid),
        );
        $ch = curl_init ();
        curl_setopt ( $ch, CURLOPT_URL, $push_api_url );
        curl_setopt ( $ch, CURLOPT_POST, 1 );
        curl_setopt ( $ch, CURLOPT_HEADER, 0 );
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt ( $ch, CURLOPT_POSTFIELDS, $post_data );
        curl_setopt ( $ch, CURLOPT_HTTPHEADER, array("Expect:"));
        $return = curl_exec ( $ch );
        curl_close ( $ch );
        return $return;
    }
}

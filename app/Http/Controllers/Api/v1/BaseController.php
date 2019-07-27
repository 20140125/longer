<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;
use App\Http\Controllers\Utils\Rsa;
use App\Http\Controllers\Controller;
use App\Models\OAuth;
use App\Models\Role;
use App\Models\Rule;
use App\Models\Users;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use PhpParser\Node\Expr\Cast\Object_;

/**
 * todo 公共类
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
     * @var Rule $ruleModel
     */
    protected $ruleModel;
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
     * todo：构造函数
     * BaseController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->post = $request->post();
        $this->userModel = Users::getInstance();
        $this->roleModel = Role::getInstance();
        $this->ruleModel = Rule::getInstance();
        $this->oauthModel = OAuth::getInstance();
        $this->backupPath = public_path('backup/');
        //公用权限
        $common_url = [
            route('checkLogin'),
            route('apiLogout'),
            route('logSave'),
            route('menu')
        ];
        //私有权限
        $url = $request->getRequestUri();
        if (strstr($url,'?')){
            $url = substr($url,0,find_str($request->getRequestUri(),"?",2));
        }
        if (empty($this->post['token'])){
            $this->setCode(Code::ERROR,'required params missing');
        }
        $this->users = $this->userModel->getResult('remember_token',$this->post['token']) ?? $this->oauthModel->getResult('remember_token',$this->post['token']);
        Log::error(json_encode($this->users));
        if (empty($this->users)){
            $this->setCode(Code::NOT_ALLOW,'Permission denied');
        }
        if ($this->users->role_id !== 1){
            $this->role = $this->roleModel->getResult('id',$this->users->role_id,'=',['auth_url','auth_ids']);
            if (!empty($this->role) && !in_array(str_replace(['/api/v1'],['/admin'],$url),json_decode($this->role->auth_url,true)) && !in_array(asset($url),$common_url)){
                $this->setCode(Code::NOT_ALLOW,'Permission denied');
            }
        }
        if (!in_array(asset($url),$common_url)){
            unset($this->post['token']);
        }
    }

    /**
     * 设置code
     * @param $code
     * @param $message
     */
    protected function setCode($code,$message)
    {
        exit(json_encode(array('code'=>$code,'msg'=>$message)));
    }

    /**
     * 数据返回
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
     * 公钥加密
     * @param string $data
     * @return string|null
     */
    protected function publicEncrypt($data='')
    {
        return $this->rsaUtils::publicEncrypt($data);
    }

    /**
     * 私钥解密
     * @param string $data
     * @return null
     */
    protected function privateDecrypt($data='')
    {
        return $this->rsaUtils::privateDecrypt($data);
    }
}

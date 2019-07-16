<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;
use App\Http\Controllers\Utils\Rsa;
use App\Models\AdminUser;
use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * 公共类
 * Class BaseController
 * @package App\Http\Controllers\Api\v1
 */
class BaseController extends Controller
{
    /**
     * @var AdminUser $adminUserModel 用户模型
     */
    protected $adminUserModel;
    /**
     * @var Role $adminRoleModel 角色模型
     */
    protected $adminRoleModel;
    /**
     * @var array $post 请求数据
     */
    protected $post;
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
        $this->adminUserModel = AdminUser::getInstance();
        $this->adminRoleModel = Role::getInstance();
        $this->backupPath = public_path('backup/');
        //公用权限
        $common_url = [
            route('apiLogin'),
            route('captcha'),
            route('checkLogin'),
            route('logSave'),
            route('logDelete'),
            route('menu'),
            route('apiLogout'),
            route('apiTree'),
            route('apiMusicIndex'),
            route('apiMusicPlay'),
            route('wxLogin'),
            route('getOpenId'),
            route('apiMusicHistory'),
            route('apiMusicHistoryLists'),
            route('apiMusicSearch')
        ];
        //私有权限
        $url = $request->getRequestUri();
        if (empty($this->post['token']) && !in_array(asset($url),$common_url)){
            $this->setCode(Code::ERROR,'required params missing');
        }
        if (!in_array(asset($url),$common_url)){
            $users = $this->adminUserModel->getResult('remember_token',$this->post['token']);
            if (empty($users)){
                $this->setCode(Code::NOT_ALLOW,'Permission denied');
            }
            if ($users->role_id !== 1){
                $role = $this->adminRoleModel->getResult('id',$users->role_id,'=',['auth_ids','auth_url']);
                if (!empty($role) && !in_array(str_replace(['/api/v1','user'],['/admin','adminuser'],$url),json_decode($role->auth_url,true)) && !in_array(asset($url),$common_url)){
                    $this->setCode(Code::NOT_ALLOW,'Permission denied');
                }
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

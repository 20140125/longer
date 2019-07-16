<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;
use App\Http\Controllers\Utils\crypt\WXBizDataCrypt;
use App\Models\Users;
use Illuminate\Config\Repository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Validator;

/**
 * 用户管理
 * Class UsersController
 * @package App\Http\Controllers\Api\v1
 */
class UsersController extends BaseController
{
    /**
     * @var Repository|mixed 用户APPID（小程序）
     */
    protected $appid;
    /**
     * @var Repository|mixed 用户密钥（小程序）
     */
    protected $appsecret;
    /**
     * @var Repository|mixed 默认密码
     */
    protected $default_pass;

    /**
     * UsersController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->appid = config('app.app_id');
        $this->appsecret = config('app.app_secret');
        $this->default_pass = config('app.default_pass');
    }

    /**
     * 微信登陆信息
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request)
    {
        if ($request->isMethod('get')){
            return $this->ajax_return(Code::METHOD_ERROR,'error');
        }
        $result = Users::getInstance()->updateResult($this->post,'openid',$this->post['openid']);
        if (!empty($result)){
            return $this->ajax_return(Code::SUCCESS,'success');
        }
        return $this->ajax_return(Code::ERROR,'error');

    }

    /**
     * 微信获取用户的openid
     * @param Request $request
     * @return JsonResponse
     */
    public function getOpenId(Request $request)
    {
        if ($request->isMethod('get')){
            return $this->ajax_return(Code::METHOD_ERROR,'error');
        }
        $url = 'https://api.weixin.qq.com/sns/jscode2session?';
        $data = array(
            'appid' =>$this->appid,
            'secret' =>$this->appsecret,
            'js_code' =>$this->post['code'],
            'grant_type' =>'authorization_code'
        );
        $response = http_query($url.http_build_query($data));
        $parsedData = json_decode(trim($response['data']), true, 512, JSON_OBJECT_AS_ARRAY);
        if (!empty($parsedData['errcode'])){
            return $this->ajax_return(Code::ERROR,$parsedData['errmsg']);
        }
        $result = Users::getInstance()->getResult('openid',$parsedData['openid']);
        $data = array(
            'openid' =>$parsedData['openid'],
            'exp_time'=>$parsedData['expires_in']+time(),
            'session_key' =>$parsedData['session_key']
        );
        if (empty($result)){
            Users::getInstance()->addResult($data);
        }else if ($result->exp_time<time()){
            Users::getInstance()->updateResult($data,'openid',$parsedData['openid']);
        }
        return $this->ajax_return(Code::SUCCESS,'success',$parsedData);
    }

    /**
     * redis 订阅
     */
    public function publish()
    {
        $redis = Redis::connection();
        $redis->publish('user_channel','这是一个测试');
    }

    /**
     * 管理员信息
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        if ($request->isMethod('get')){
            return $this->ajax_return(Code::METHOD_ERROR,'error');
        }
        $result['userLists'] = $this->adminUserModel->getResultList();
        foreach ($result['userLists'] as &$item){
            $item->updated_at = date("Y-m-d H:i:s",$item->updated_at);
            $item->created_at = date("Y-m-d H:i:s",$item->created_at);
            unset($item->remember_token);
        }
        $result['roleLists'] = $this->adminRoleModel->getResult2('1');
        $result['default_pass'] = $this->default_pass;
        return $this->ajax_return(Code::SUCCESS,'success',$result);
    }

    /**
     * 管理员保存
     * @param Request $request
     * @return JsonResponse
     */
    public function save(Request $request)
    {
        if ($request->isMethod('get')){
            return $this->ajax_return(Code::METHOD_ERROR,'error');
        }
        $validate = Validator::make($this->post,$this->rule(3));
        if ($validate->fails()){
            return $this->ajax_return(Code::ERROR,$validate->errors()->first());
        }
        $data = $this->post;
        $data['password'] = empty($data['password'])?'123456789':$data['password']; //设置默认密码
        $salt = get_round_num(8);
        $args = array(
            'username' =>$data['username'],
            'email' =>$data['email'],
            'salt' => $salt,
            'status' =>$data['status'],
            'role_id' =>$data['role_id'],
            'password' =>md5(md5($data['password']).$salt),
            'ip_address' =>$request->ip(),
            'created_at' =>time(),
            'updated_at' =>time()
        );
        $args['role_id'] = $this->adminRoleModel->getResult('role_name',$args['role_id'])->id;
        $result = $this->adminUserModel->addResult($args);
        if ($result){
            return $this->ajax_return(Code::SUCCESS,'add user success');
        }
        return $this->ajax_return(Code::ERROR,'add user error');
    }
    /**
     * 管理员更新
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request)
    {
        if ($request->isMethod('get')){
            return $this->ajax_return(Code::METHOD_ERROR,'error');
        }
        //区分修改类型 0 只是修改显示状态 1 修改管理员信息
        $data = $this->post;
        $flag = !empty($data['act'])?0:1;
        $salt = get_round_num(8);
        switch ($flag){
            case 0:
                $validate = Validator::make($this->post,['status' => 'required|string|in:1,2','id'=>'required|integer|gt:1'],['id.gt'=>'Permission denied']);
                if ($validate->fails()){
                    return $this->ajax_return(Code::ERROR,$validate->errors()->first());
                }
                $args = array('status' =>$data['status']);
                break;
            case 1:
                $rule = $this->rule(1);
                $args = array(
                    'username' =>$data['username'],
                    'email' =>$data['email'],
                    'status' =>$data['status'],
                    'role_id' =>empty($data['role_id'])?0:$data['role_id'],
                    'ip_address' =>$request->ip(),
                    'created_at' =>strtotime($data['created_at']),
                    'updated_at' =>time()
                );
                $args['role_id'] = $this->adminRoleModel->getResult('role_name',$args['role_id'])->id;
                if (!empty($data['password'])){
                    $rule = $this->rule(2);
                    $args['salt'] = $salt;
                    $args['password'] = md5(md5($data['password']).$salt);
                }
                $validate = Validator::make($this->post,$rule);
                if ($validate->fails()){
                    return $this->ajax_return(Code::ERROR,$validate->errors()->first());
                }
                break;
            default:
                return $this->ajax_return(Code::ERROR,'input args error');
                break;
        }
        $result = $this->adminUserModel->updateResult($args,'id',$data['id']);
        if ($result){
            return $this->ajax_return(Code::SUCCESS,'update user success');
        }
        return $this->ajax_return(Code::ERROR,'update user error');
    }

    /**
     * 删除管理员用户
     * @param Request $request
     * @return JsonResponse
     */
    public function delete(Request $request)
    {
        if ($request->isMethod('get')){
            return $this->ajax_return(Code::METHOD_ERROR,'error');
        }
        $validate = Validator::make($this->post,['id'=>'required|integer|gt:1'],['id.gt'=>'Permission denied']);
        if ($validate->fails()) {
            return $this->ajax_return(Code::ERROR,$validate->errors()->first());
        }
        $result = $this->adminUserModel->deleteResult('id',$this->post['id']);
        if ($result){
            return $this->ajax_return(Code::SUCCESS,'delete user success');
        }
        return $this->ajax_return(Code::ERROR,'delete user error');
    }

    /**
     * 验证规则
     * @param $status 1 不验证密码 （更新）  2 验证密码 （更新）  3 验证用户名（添加）
     * @return array
     */
    protected function rule($status)
    {
        $rule = array();
        switch ($status){
            case 1:
                $rule = [
                    'username' => 'required|between:4,16|string',
                    'email' => 'required|email',
                    'status'   => 'required|string|between:1,2'
                ];
                break;
            case 2:
                $rule= [
                    'username' => 'required|between:4,16|string',
                    'email' => 'required|email',
                    'password' => 'required|string|between:6,16',
                    'status'   => 'required|string|in:1,2'
                ];
                break;
            case 3:
                $rule= [
                    'username' => 'required|between:4,16|string|unique:os_admin_users',
                    'email' => 'required|email',
                    'password' => 'required|string|between:6,16',
                    'status'   => 'required|string|in:1,2'
                ];
                break;
        }
        return $rule;
    }
}

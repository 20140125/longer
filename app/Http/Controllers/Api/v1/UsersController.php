<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;
use App\Models\Users;
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
     * UsersController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->appid = config('app.app_id');
        $this->appsecret = config('app.app_secret');
    }

    /**
     * TODO: 微信获取用户的openid
     * @return JsonResponse
     */
    public function getOpenId()
    {
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
        return $this->ajax_return(Code::SUCCESS,'successfully',$parsedData);
    }

    /**
     * TODO: 微信登陆信息
     * @return JsonResponse
     */
    public function login()
    {
        $result = Users::getInstance()->updateResult($this->post,'openid',$this->post['openid']);
        if (!empty($result)){
            return $this->ajax_return(Code::SUCCESS,'login successfully');
        }
        return $this->ajax_return(Code::ERROR,'error');

    }
    /**
     * TODO: 管理员信息
     * @return JsonResponse
     */
    public function index()
    {
        $result['userLists'] = $this->userModel->getResultList();
        foreach ($result['userLists'] as &$item){
            $item->updated_at = date("Y-m-d H:i:s",$item->updated_at);
            $item->created_at = date("Y-m-d H:i:s",$item->created_at);
        }
        $result['roleLists'] = $this->roleModel->getResult2('1',['id','role_name']);
        return $this->ajax_return(Code::SUCCESS,'successfully',$result);
    }

    /**
     * TODO: 管理员保存
     * @param Request $request
     * @return JsonResponse
     */
    public function save(Request $request)
    {
        $validate = Validator::make($this->post,$this->rule(3));
        if ($validate->fails()){
            return $this->ajax_return(Code::ERROR,$validate->errors()->first());
        }
        $this->post['password'] = md5(md5($this->post['password']).$this->post['salt']);
        $this->post['role_id'] = $this->roleModel->getResult('id',$this->post['role_id'])->id;
        $this->post['ip_address'] = $request->ip();
        $result = $this->userModel->addResult($this->post);
        if ($result){
            return $this->ajax_return(Code::SUCCESS,'add user successfully');
        }
        return $this->ajax_return(Code::ERROR,'add user error');
    }
    /**
     * TODO: 管理员更新
     * @return JsonResponse
     */
    public function update()
    {
        //修改用户禁用状态
        if (!empty($this->post['act'])){
            $validate = Validator::make($this->post,$this->rule(4),['id.gt'=>'Permission denied']);
            if ($validate->fails()){
                return $this->ajax_return(Code::ERROR,$validate->errors()->first());
            }
            unset($this->post['act']);
            $result = $this->userModel->updateResult($this->post,'id',$this->post['id']);
            if (!empty($result)){
                return $this->ajax_return(Code::SUCCESS,'update users status successfully');
            }
            return $this->ajax_return(Code::SUCCESS,'update users status error');
        }
        $password = $this->userModel->getResult('id',$this->post['id'])->password;
        $this->post['created_at'] = strtotime($this->post['created_at']);
        $this->post['updated_at'] = time();
        //用户没有修改密码
        if ($password == $this->post['password']){
            $validate = Validator::make($this->post,$this->rule(1));
            if ($validate->fails()){
                return $this->ajax_return(Code::ERROR,$validate->errors()->first());
            }
            unset($this->post['password']);
            $result = $this->userModel->updateResult($this->post,'id',$this->post['id']);
            if (!empty($result)){
                return $this->ajax_return(Code::SUCCESS,'update users successfully');
            }
            return $this->ajax_return(Code::SUCCESS,'update users error');
        }
        //用户修改密码
        $validate = Validator::make($this->post,$this->rule(2));
        if ($validate->fails()){
            return $this->ajax_return(Code::ERROR,$validate->errors()->first());
        }
        $this->post['salt'] = get_round_num(8);
        $this->post['password'] = md5(md5($this->post['password']).$this->post['salt']);
        $result = $this->userModel->updateResult($this->post,'id',$this->post['id']);
        if (!empty($result)){
            return $this->ajax_return(Code::SUCCESS,'update users successfully');
        }
        return $this->ajax_return(Code::SUCCESS,'update users error');
    }

    /**
     * TODO: 删除管理员用户
     * @return JsonResponse
     */
    public function delete()
    {
        $validate = Validator::make($this->post,['id'=>'required|integer|gt:1'],['id.gt'=>'Permission denied']);
        if ($validate->fails()) {
            return $this->ajax_return(Code::ERROR,$validate->errors()->first());
        }
        $result = $this->userModel->deleteResult('id',$this->post['id']);
        if ($result){
            return $this->ajax_return(Code::SUCCESS,'delete user successfully');
        }
        return $this->ajax_return(Code::ERROR,'delete user error');
    }

    /**
     * TODO: 验证规则
     * @param $status 1 不验证密码 （更新）  2 验证密码 （更新）  3 验证用户名（添加）
     * @return array
     */
    protected function rule($status)
    {
        switch ($status){
            case 1:
                $rule = [
                    'username' => 'required|between:4,16|string',
                    'email' => 'required|email',
                    'status'   => 'required|integer|between:1,2',
                    'phone_number' => 'required|size:11',
                    'role_id' => 'required|integer'
                ];
                break;
            case 2:
                $rule= [
                    'username' => 'required|between:4,16|string',
                    'email' => 'required|email',
                    'password' => 'required|string|between:6,16',
                    'salt' => 'required|string|size:8',
                    'status'   => 'required|integer|in:1,2',
                    'phone_number' => 'required|size:11',
                    'role_id' => 'required|integer'
                ];
                break;
            case 3:
                $rule= [
                    'username' => 'required|between:4,16|string|unique:os_users',
                    'email' => 'required|email',
                    'password' => 'required|string|between:6,16',
                    'status'   => 'required|integer|in:1,2',
                    'role_id' => 'required|integer',
                    'salt' => 'required|string|size:8',
                    'created_at' => 'required|digits:10',
                    'updated_at' => 'required|digits:10'
                ];
                break;
            case 4:
                $rule = [
                    'status'   => 'required|string|between:1,2',
                    'id' => 'required|integer|gt:1'
                ];
                break;
            default:
                $rule = [];
                break;
        }
        return $rule;
    }
}

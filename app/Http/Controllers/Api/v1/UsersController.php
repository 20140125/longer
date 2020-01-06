<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;
use App\Models\OAuth;
use App\Models\Push;
use App\Models\UserCenter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;


/**
 * TODO: 用户管理
 * Class UsersController
 * @author <fl140125@gmail.com>
 * @package App\Http\Controllers\Api\v1
 */
class UsersController extends BaseController
{
    /**
     * TODO: 管理员信息
     * @return JsonResponse
     */
    public function index()
    {
        $result = $this->userModel->getResultList($this->users,$this->post['page'],$this->post['limit']);
        foreach ($result['data'] as &$item){
            $item->updated_at = date("Y-m-d H:i:s",$item->updated_at);
            $item->created_at = date("Y-m-d H:i:s",$item->created_at);
        }
        $result['roleLists'] = $this->roleModel->getResult2('1',['id','role_name']);
        return $this->ajax_return(Code::SUCCESS,'successfully',$result);
    }

    /**
     * TODO: 管理员保存
     * @param Request $request (password:密码，role_id:角色ID，ip_address:IP地址)
     * @return JsonResponse
     */
    public function save(Request $request)
    {
        $this->validatePost($this->rule(3));
        $this->post['password'] = md5(md5($this->post['password']).$this->post['salt']);
        $this->post['ip_address'] = $request->ip();
        $this->post['avatar_url'] = empty($this->post['avatar_url']) ? '0' : $this->post['avatar_url'];
        $result = $this->userModel->addResult($this->post);
        if ($result){
            if (!empty($this->post['avatar_url'])) {
                OAuth::getInstance()->updateResult(['uid'=>$result],'remember_token',$this->post['remember_token']);
            }
            return $this->ajax_return(Code::SUCCESS,'add user successfully');
        }
        return $this->ajax_return(Code::ERROR,'add user error');
    }

    /**
     * TODO：获取绑定用户信息
     * @return JsonResponse
     */
    public function getBindInfo()
    {
        $this->validatePost(['remember_token'=>'required|string|size:32']);
        $result = $this->userModel->getResult('remember_token',$this->post['remember_token']);
        if (!empty($result)) {
            return $this->ajax_return(Code::SUCCESS,'successfully',$result);
        }
        return $this->ajax_return(Code::ERROR,'No bound account information');
    }
    /**
     * TODO: 管理员更新
     * @param string act
     * @param integer id
     * @param string username
     * @param string password
     * @param string created_at
     * @param string updated_at
     * @return JsonResponse
     */
    public function update()
    {
        //修改用户禁用状态
        if (!empty($this->post['act'])){
            $this->validatePost($this->rule(4),['id.gt'=>'Permission denied']);
            unset($this->post['act']);
            $result = $this->userModel->updateResult($this->post,'id',$this->post['id']);
            if (!empty($result)){
                return $this->ajax_return(Code::SUCCESS,'update users status successfully');
            }
            return $this->ajax_return(Code::ERROR,'update users status error');
        }
        $password = $this->userModel->getResult('id',$this->post['id'])->password;
        $this->post['created_at'] = strtotime($this->post['created_at']);
        $this->post['updated_at'] = time();
        if ($password == $this->post['password']){
            //用户没有修改密码
            $this->validatePost($this->rule(1));
        } else {
            //用户修改密码
            $this->post['salt'] = get_round_num(8);
            $this->post['password'] = md5(md5($this->post['password']).$this->post['salt']);
            $this->validatePost($this->rule(2));
        }
        $result = $this->userModel->updateResult($this->post,'id',$this->post['id']);
        if (empty($result)){
            return $this->ajax_return(Code::ERROR,'update users error');
        }
        //修改密码站内通知
        $this->post['info'] = '你的密码修改成功，新密码是：'.$this->post['password'];
        $this->post['uid'] = md5($this->post['username']);
        $this->post['status'] = 1;
        $this->pushMessage();
        $message = array(
            'username' => $this->post['username'],
            'info' => $this->post['info'],
            'uid'  => md5($this->post['username']),
            'state' => $this->post['state'],
            'title' => '修改密码',
            'status' => 1,
            'created_at' => time()
        );
        Push::getInstance()->addResult($message);
        return $this->ajax_return(Code::SUCCESS,'update users successfully');
    }

    /**
     * TODO:获取个人信息
     * @param string token
     * @return JsonResponse
     */
    public function center()
    {
        $result = UserCenter::getInstance()->getResult('token',$this->users->remember_token);
        $result->email = $this->users->email;
        $result->tags = empty($result->tags) ? array() : json_decode($result->tags,true);
        $result->ip_address = json_decode($result->ip_address,true);
        $result->local = json_decode($result->local,true);
        return $this->ajax_return(Code::SUCCESS,'successfully',$result);
    }

    /**
     * TODO:保存个人信息
     * @param string u_name 用户名
     * @param integer id
     * @param array tags 标签
     * @param integer user_status 用户状态
     * @param string ip_address IP地址
     * @param integer u_type 用户类型
     * @param string desc 描述
     * @param integer notice_status 通知状态
     * @param integer uid ID
     * @param array local 地址
     * @return JsonResponse
     */
    public function saveCenter()
    {
        $this->validatePost(
            [
                'u_name'=>'required|string',
                'id'=>'required|integer','desc'=>'required|string|max:128',
                'tags'=>'required|Array|max:128','notice_status'=>'required|integer|in:1,2',
                'user_status'=>'required|integer|in:1,2','uid'=>'required|integer',
                'ip_address' => 'required|Array','local'=>'required|Array'
            ]
        );
        unset($this->post['email']);
        $result = UserCenter::getInstance()->updateResult($this->post,'id',$this->post['id']);
        if (!empty($result)) {
            return $this->ajax_return(Code::SUCCESS,'update user information successfully');
        }
        return $this->ajax_return(Code::SUCCESS,'update user information error');
    }

    /**
     * TODO: 删除管理员用户
     * @param integer id
     * @return JsonResponse
     */
    public function delete()
    {
        $this->validatePost(['id'=>'required|integer|gt:1'],['id.gt'=>'Permission denied']);
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
                    'username' => 'required|max:16|string',
                    'email' => 'required|email',
                    'status'   => 'required|integer|in:1,2',
                    'phone_number' => 'required|size:11',
                    'role_id' => 'required|integer'
                ];
                break;
            case 2:
                $rule= [
                    'username' => 'required|max:16|string',
                    'email' => 'required|email',
                    'password' => 'required|string|between:6,32',
                    'salt' => 'required|string|size:8',
                    'status'   => 'required|integer|in:1,2',
                    'phone_number' => 'required|size:11',
                    'role_id' => 'required|integer'
                ];
                break;
            case 3:
                $rule= [
                    'username' => 'required|max:16|string|unique:os_users',
                    'email' => 'required|email|unique:os_users',
                    'password' => 'required|string|between:6,32',
                    'status'   => 'required|integer|in:1,2',
                    'role_id' => 'required|integer',
                    'salt' => 'required|string|size:8',
                    'created_at' => 'required|digits:10',
                    'updated_at' => 'required|digits:10'
                ];
                break;
            case 4:
                $rule = [
                    'status'   => 'required|string|in:1,2',
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

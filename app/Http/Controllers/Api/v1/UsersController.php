<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;
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
        $this->validatePost($this->rule(3));
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
        //用户没有修改密码
        if ($password == $this->post['password']){
            $this->validatePost($this->rule(1));
            unset($this->post['password']);
            $result = $this->userModel->updateResult($this->post,'id',$this->post['id']);
            if (!empty($result)){
                return $this->ajax_return(Code::SUCCESS,'update users successfully');
            }
            return $this->ajax_return(Code::ERROR,'update users error');
        }
        //用户修改密码
        $this->validatePost($this->rule(2));
        $pass = $this->post['password'];
        $this->post['salt'] = get_round_num(8);
        $this->post['password'] = md5(md5($this->post['password']).$this->post['salt']);
        $result = $this->userModel->updateResult($this->post,'id',$this->post['id']);
        if (!empty($result)){
            //修改密码站内通知
            $this->post['info'] = '你的密码修改成功，新密码是：'.$pass;
            $this->post['username'] = 'admin';
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
        return $this->ajax_return(Code::ERROR,'update users error');
    }

    /**
     * TODO:获取个人信息
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
     * @return JsonResponse
     */
    public function saveCenter()
    {
        $this->validatePost(
            [
                'u_name'=>'required|string','u_type'=>'required|integer',
                'id'=>'required|integer','desc'=>'required|string|max:128',
                'tags'=>'required|Array|max:128','notice_status'=>'required|integer|in:1,2',
                'user_status'=>'required|integer|in:1,2','uid'=>'required|integer',
                'ip_address' => 'required|Array','local'=>'required|Array'
            ]
        );
        unset($this->post['email']);
        unset($this->post['avatarUrl']);
        $result = UserCenter::getInstance()->updateResult($this->post,'id',$this->post['id']);
        if (!empty($result)) {
            return $this->ajax_return(Code::SUCCESS,'update user information successfully');
        }
        return $this->ajax_return(Code::SUCCESS,'update user information error');
    }

    /**
     * TODO: 删除管理员用户
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
                    'username' => 'required|between:4,16|string',
                    'email' => 'required|email',
                    'status'   => 'required|integer|between:1,2',
                    'phone_number' => 'required|size:11',
                    'role_id' => 'required|integer|in:1'
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

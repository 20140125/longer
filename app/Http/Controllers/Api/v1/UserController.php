<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class UserController extends BaseController
{
    /**
     * 管理员列表
     * @param Request $request
     * @return Factory|JsonResponse|View
     */
    public function index(Request $request)
    {
        if ($request->isMethod('get')){
            return view('admin.user.index');
        }
        $user = $this->userModel->getResultList($this->post['page']??1,$this->post['limit']??20);
        foreach ($user['data'] as &$item){
            $item->updated_at = date("Y-m-d H:i:s",$item->updated_at);
            $item->created_at = date("Y-m-d H:i:s",$item->created_at);
        }
        $result['user']  = $user;
        $roleList = $this->roleModel->getResult2(1);
        $result['role'] = $roleList;
        return $this->ajax_return(self::SUCCESS,'success','',$result);
    }


    /**
     * todo 管理员保存
     * @param Request $request
     * @return JsonResponse
     */
    public function save(Request $request)
    {
        if ($request->isMethod('get')){
            return $this->ajax_return(self::METHOD_ERROR,'error');
        }
        $validate = Validator::make($request->post(),$this->rule(3));
        if ($validate->fails()){
            return $this->ajax_return(self::ERROR,$validate->errors()->first());
        }
        $data = $this->post;
        $salt = $this->get_round_num(8);
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
        $result = $this->userModel->addResult($args);
        if ($result){
            $this->sys_log('管理员保存');
            return $this->ajax_return(self::SUCCESS,'add user success',route('admin-user-index'));
        }
        return $this->ajax_return(self::ERROR,'add user error');
    }

    /**
     * todo 管理员更新
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request)
    {
        if ($request->isMethod('get')){
            return $this->ajax_return(self::METHOD_ERROR,'error');
        }
        $data = $this->post;
        $flag = !empty($data['act']) ? 0 : 1;
        $salt = $this->get_round_num(8);
        switch ($flag){
            case 0:
                $validate = Validator::make($request->post(),['status' => 'required|in:1,2','id'=>'required|integer|gt:1']);
                if ($validate->fails()){
                    return $this->ajax_return(self::ERROR,$validate->errors()->first());
                }
                $args = array('status' =>$data['status']);
                break;
            case 1:
                $rule = $this->rule(1);
                $args = array(
                    'username' =>$data['username'],
                    'email' =>$data['email'],
                    'status' =>$data['status'],
                    'role_id' =>empty($data['role_id']) ? 0 : $data['role_id'],
                    'ip_address' =>$request->ip(),
                    'created_at' =>time(),
                    'updated_at' =>time(),
                    'phone_number' => $data['phone_number']
                );
                $password = $this->userModel->getResult('email',$this->post['email'])->password;
                if (!empty($data['password']) && $password!=$data['password']){
                    $rule = $this->rule(2);
                    $args['salt'] = $salt;
                    $args['password'] = md5(md5($data['password']).$salt);
                }
                $validate = Validator::make($request->post(),$rule);
                if ($validate->fails()){
                    return $this->ajax_return(self::ERROR,$validate->errors()->first());
                }
                break;
            default:
                return $this->ajax_return(self::ERROR,'input args error');
                break;
        }
        $result = $this->userModel->updateResult($args,'id',$data['id']);
        if ($result){
            $this->sys_log('管理员更新');
            return $this->ajax_return(self::SUCCESS,'update user success',route('admin-user-index'));
        }
        return $this->ajax_return(self::ERROR,'update user error');
    }

    /**
     * todo 删除管理员用户
     * @param Request $request
     * @return JsonResponse
     */
    public function delete(Request $request)
    {
        if ($request->isMethod('get')){
            return $this->ajax_return(self::METHOD_ERROR,'error');
        }
        $validate = Validator::make($this->post,['id'=>'required|integer|gt:1']);
        if ($validate->fails()) {
            return $this->ajax_return(self::ERROR,$validate->errors()->first());
        }
        $result = $this->userModel->deleteResult('id',$this->post['id']);
        if ($result){
            $this->sys_log('删除管理员用户');
            return $this->ajax_return(self::SUCCESS,'delete user success',route('admin-user-index'));
        }
        return $this->ajax_return(self::ERROR,'delete user error');
    }

    /**
     * 验证规则
     * @param $status 1 不验证密码 （更新）  2 验证密码 （更新）  3 验证用户名（添加）
     * @return array
     */
    protected function rule($status)
    {
        $rule = [];
        switch ($status){
            case 1:
                $rule = [
                    'username' => 'required|between:5,16|string',
                    'email' => 'required|email',
                    'status'   => 'required|in:1,2'
                ];
                break;
            case 2:
                $rule= [
                    'username' => 'required|between:5,16|string',
                    'email' => 'required|email',
                    'password' => 'required|string|between:8,16',
                    'status'   => 'required|in:1,2'
                ];
                break;
            case 3:
                $rule= [
                    'username' => 'required|between:5,16|string|unique:os_user',
                    'email' => 'required|email',
                    'password' => 'required|string|between:8,16',
                    'status'   => 'required|in:1,2'
                ];
                break;
            default:
                break;
        }
        return $rule;
    }
}

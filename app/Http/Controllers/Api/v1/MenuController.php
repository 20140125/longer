<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;
use App\Models\UserCenter;
use Illuminate\Http\JsonResponse;

/**
 * 导航栏
 * Class MenuController
 * @author <fl140125@gmail.com>
 * @package App\Http\Controllers\Api\v1
 */
class MenuController extends BaseController
{
    /**
     * todo 权限列表
     * @return JsonResponse
     */
    public function getMenu()
    {
        $role = $this->roleModel->getResult('id',$this->users->role_id);
        if (empty($role)){
            set_code(Code::NOT_ALLOW);
            return $this->ajax_return(Code::NOT_ALLOW,'permission denied');
        }
        switch ($this->users->role_id){
            case 1:
                $authLists = $this->authModel->getAuthTree();
                break;
            default:
                $authLists = $this->authModel->getAuthTree(json_decode($this->role->auth_ids,true));
                break;
        }
        return $this->ajax_return(Code::SUCCESS,'successfully',$authLists);
    }
    /**
     * todo 用户合法性
     * @return JsonResponse
     */
    public function check()
    {
        $role = $this->roleModel->getResult('id',$this->users->role_id);
        if (!empty($role)){
            return $this->ajax_return(Code::SUCCESS,'permission',
                [
                    'auth'=>$role->auth_url,
                    'token'=>$this->users->remember_token,
                    'username'=>$this->users->username,
                    'socket'=>config('app.socket_url'),
                    'avatar_url' => $this->users->username == 'admin' ? config('app.avatar_url') :$this->users->avatar_url,
                    'websocket'=>config('app.websocket')
                ]
            );
        }
        set_code(Code::NOT_ALLOW);
        return $this->ajax_return(Code::NOT_ALLOW,'permission denied');
    }
    /**
     * todo  退出登陆
     * @return JsonResponse
     */
    public function logout()
    {
        unset($this->users->notice_status);
        unset($this->users->user_status);
        if (isset($this->users->salt)){
            $where[] = array('u_type',2);
            $this->users->remember_token = md5(md5($this->users->password).time());
            $result = $this->userModel->updateResult(object_to_array($this->users),'id',$this->users->id);
        }else{
            $where[] = array('u_type',1);
            $this->users->remember_token = md5(md5($this->users->remember_token).time());
            $result = $this->oauthModel->updateResult(object_to_array($this->users),'id',$this->users->id);
        }
        if (!empty($result)){
            $where[] = array('u_name',$this->users->username);
            UserCenter::getInstance()->updateResult(array('token'=>$this->users->remember_token,'type'=>'logout'),$where);
            return $this->ajax_return(Code::SUCCESS,'logout system successfully');
        }
        return $this->ajax_return(Code::ERROR,'logout system failed');
    }
}

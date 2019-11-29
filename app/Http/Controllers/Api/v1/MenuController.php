<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;
use App\Models\OAuth;
use App\Models\UserCenter;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * 导航栏
 * Class MenuController
 * @author <fl140125@gmail.com>
 * @package App\Http\Controllers\Api\v1
 */
class MenuController extends BaseController
{
    /**
     * TODO: 权限列表
     * @param string token 用户标识
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
     * TODO: 用户合法性
     * @param string token 用户标识
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
     * TODO:  退出登陆
     * @param string token 用户标识
     * @return JsonResponse
     */
    public function logout()
    {
        if (isset($this->users->salt)) {
            $where[] = array('u_type',2);
            $this->users->remember_token = md5(md5($this->users->password).time());
            $result = $this->userModel->updateResult(object_to_array($this->users),'id',$this->users->id);
        } else {
            $where[] = array('u_type',1);
            $this->users->remember_token = md5(md5($this->users->remember_token).time());
            $result = $this->oauthModel->updateResult(object_to_array($this->users),'id',$this->users->id);
        }
        if (!empty($result)) {
            $where[] = array('u_name',$this->users->username);
            UserCenter::getInstance()->updateResult(array('token'=>$this->users->remember_token,'type'=>'logout'),$where);
            return $this->ajax_return(Code::SUCCESS,'logout system successfully');
        }
        return $this->ajax_return(Code::ERROR,'logout system failed');
    }

    /**
     * TODO：获取总数
     * @param string token 用户标识
     * @return JsonResponse
     */
    public function getCountData()
    {
        $result['oauthUser'] = DB::table('os_oauth')->count();
        $result['push'] = DB::table('os_push')->count();
        $result['systemLog'] = DB::table('os_system_log')->count();
        return $this->ajax_return(Code::SUCCESS,'successfully',$result);
    }
}

<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\CommonController;
use App\Http\Controllers\Utils\Code;
use App\Models\OAuth;
use App\Models\TimeLine;
use App\Models\UserCenter;
use App\Models\Users;
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
                    'avatar_url' => $this->users->username == 'admin' ? config('app.avatar_url') : $this->users->avatar_url,
                    'websocket'=>config('app.websocket'),
                    'role_id' => md5($this->users->role_id),
                    'uuid' => empty($this->users->uuid) ? '' :$this->users->uuid,
                    'local' => config('app.url'),
                    'adcode' => in_array(get_server_ip(),['10.97.227.81']) ? '440305' : CommonController::getInstance()->getCityCode(),
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
        $remember_token = md5(md5($this->users->remember_token).time());
        //授权列表
        OAuth::getInstance()->updateResult(['remember_token'=>$remember_token],'remember_token',$this->users->remember_token);
        //用户表
        Users::getInstance()->updateResult(['remember_token'=>$remember_token],'remember_token',$this->users->remember_token);
        //用户信息表
        UserCenter::getInstance()->updateResult(array('token'=>$remember_token,'type'=>'logout'),'token',$this->users->remember_token);
        return $this->ajax_return(Code::SUCCESS,'logout system successfully');
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
        $result['timeline'] = TimeLine::getInstance()->getResultLists(1,15);
        return $this->ajax_return(Code::SUCCESS,'successfully',$result);
    }
}

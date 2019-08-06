<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;
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
                $authLists = $this->ruleModel->getAuthTree();
                break;
            default:
                $authLists = $this->ruleModel->getAuthTree(json_decode($this->role->auth_ids,true));
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
            return $this->ajax_return(Code::SUCCESS,'permission',['auth'=>$role->auth_url,'token'=>$this->users->remember_token,'username'=>$this->users->username]);
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
        if (isset($this->users->salt)){
            $this->users->remember_token = md5(md5($this->users->password).time());
            $result = $this->userModel->updateResult(object_to_array($this->users),'id',$this->users->id);
        }else{
            $this->users->remember_token = md5(md5($this->users->remember_token).time());
            $result = $this->oauthModel->updateResult(object_to_array($this->users),'id',$this->users->id);
        }
        if (!empty($result)){
            return $this->ajax_return(Code::SUCCESS,'logout system successfully');
        }
        return $this->ajax_return(Code::ERROR,'logout system failed');
    }
}

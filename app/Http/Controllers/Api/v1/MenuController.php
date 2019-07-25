<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
     * @param Request $request
     * @return JsonResponse
     */
    public function getMenu(Request $request)
    {
        if ($request->isMethod('get')){
            return ajax_return(Code::METHOD_ERROR,'error');
        }
        $validate = Validator::make($this->post,['token'],['token'=>'required|string|length:32']);
        if ($validate->fails()){
            return $this->ajax_return(Code::ERROR,'error',$validate->errors()->first());
        }
        $username = $this->userModel->getResult('access_token',$this->post['token']);
        if (empty($username)){
            return $this->ajax_return(Code::ERROR,'error');
        }
        switch ($username->role_id){
            case 1:
                $authLists = $this->ruleModel->getAuthTree();
                break;
            default:
                $role = $this->roleModel->getResult('id',$username->role_id,'=',['auth_ids']);
                $authLists = $this->ruleModel->getAuthTree(json_decode($role->auth_ids,true));
                break;
        }
        return $this->ajax_return(Code::SUCCESS,'successfully',$authLists);
    }
}

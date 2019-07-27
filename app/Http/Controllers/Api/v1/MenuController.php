<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        $role = $this->roleModel->getResult('id',$this->users->role_id);
        if (empty($role)){
            return $this->ajax_return(Code::ERROR,'permission denied');
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
}

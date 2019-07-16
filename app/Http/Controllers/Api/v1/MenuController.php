<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;
use App\Models\AuthRule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * 导航栏
 * Class MenuController
 * @package App\Http\Controllers\Api\v1
 */
class MenuController extends BaseController
{
    /**
     * @var array $authLists 权限列表
     * @var AuthRule $adminAuthModel 权限模型
     */
    protected $authLists = [],$adminAuthModel;

    /**
     * 构造函数
     * MenuController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->adminAuthModel = AuthRule::getInstance();
    }

    /**
     * 权限列表
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
        $username = $this->adminUserModel->getResult('remember_token',$this->post['token']);
        if (empty($username)){
            return $this->ajax_return(Code::ERROR,'error');
        }
        switch ($username->role_id){
            case 1:
                $this->authLists = $this->adminAuthModel->getAuthTree();
                break;
            default:
                $role = $this->adminRoleModel->getResult('id',$username->role_id,'=',['auth_ids','auth_url']);
                $this->authLists = $this->adminAuthModel->getAuthTree(0,json_decode($role->auth_ids,true));
                break;
        }
        foreach ($this->authLists as &$item){
            foreach ($item->__children as &$row){
                $row->href = strtolower($row->href);
            }
        }
        return $this->ajax_return(Code::SUCCESS,'success',$this->authLists);
    }
    /**
     * 权限树形结构列表
     * @return false|string
     */
    public function tree()
    {
        $authLists = $this->adminAuthModel->getAuthList();
        $result = get_tree($authLists,0);
        return $this->ajax_return(Code::SUCCESS,'success',$result);
    }
}

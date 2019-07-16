<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;
use App\Models\AuthRule;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

/**
 * 角色管理
 * Class RoleController
 * @package App\Http\Controllers\Api\v1
 */
class RoleController extends BaseController
{
    /**
     * @var AuthRule $adminAuthModel 权限模型
     */
    protected $adminAuthModel;

    /**
     * RoleController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->adminAuthModel = AuthRule::getInstance();
    }

    /**
     * todo：角色列表
     * @param Request $request
     * @return Factory|JsonResponse|View
     */
    public function index(Request $request)
    {
        if ($request->isMethod('get')){
            return $this->ajax_return(Code::METHOD_ERROR,'error');
        }
        $roleLists = $this->adminRoleModel->getResult2();
        foreach ($roleLists as &$item){
            $item->created_at = date("Y-m-d H:i:s",$item->created_at);
            $item->updated_at = date("Y-m-d H:i:s",$item->updated_at);
        }
        $authLists = $this->adminAuthModel->getAuthList();
        return $this->ajax_return(Code::SUCCESS,'success',['role'=>$roleLists,'auth'=>$authLists]);
    }

    /**
     * todo：角色保存
     * @param Request $request
     * @return JsonResponse
     */
    public function save(Request $request)
    {
        if ($request->isMethod('get')){
            return $this->ajax_return(Code::METHOD_ERROR,'error');
        }
        $validate = Validator::make($this->post,
            [
                'status'=>'required|in:1,2',
                'auth_ids'=>'required|string',
                'role_name'=>'required|string'
            ]
        );
        if ($validate->fails()){
            return $this->ajax_return(Code::ERROR,$validate->errors()->first());
        }
        $data = $this->post;
        $auth_ids = explode(",",$data['auth_ids']);
        $authLists = $this->adminAuthModel->getResult('id',$auth_ids,'in',['href']);
        $auth_url = array();
        foreach ($authLists as $item){
            $auth_url[] = strtolower($item->href);
        }
        $data['auth_url'] = json_encode($auth_url);
        $data['auth_ids'] = json_encode($auth_ids);
        $data['created_at'] = time();
        $data['updated_at'] = time();
        $result = $this->adminRoleModel->addResult($data);
        if (!empty($result)){
            return $this->ajax_return(Code::SUCCESS,'role add success');
        }
        return $this->ajax_return(Code::ERROR,'role add error');
    }

    /**
     * todo：角色更新
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request)
    {
        if ($request->isMethod('get')){
            return $this->ajax_return(Code::METHOD_ERROR,'error');
        }
        if (!empty($this->post['act']) && $this->post['act'] == 'status'){
            unset($this->post['act']);
            $validate = Validator::make($this->post,
                [
                    'status'=>'required|in:1,2',
                    'id'=>'required|integer|gt:1'
                ],[
                    'id.gt'=>'Permission denied'
                ]
            );
            if ($validate->fails()){
                return $this->ajax_return(Code::ERROR,$validate->errors()->first());
            }
            $result = $this->adminRoleModel->updateResult($this->post,'id',$this->post['id']);
            if (!empty($result)){
                return $this->ajax_return(Code::SUCCESS,'role update status success');
            }
            return $this->ajax_return(Code::ERROR,'role update status error');
        }
        $validate = Validator::make($this->post,
            [
                'status'=>'required|in:1,2',
                'auth_ids'=>'required|string',
                'role_name'=>'required|string'
            ]
        );
        if ($validate->fails()){
            return $this->ajax_return(Code::ERROR,$validate->errors()->first());
        }
        $data = $this->post;
        $auth_ids = explode(",",$data['auth_ids']);
        $authLists = $this->adminAuthModel->getResult('id',$auth_ids,'in',['href']);
        $auth_url = array();
        foreach ($authLists as $item){
            $auth_url[] = strtolower($item->href);
        }
        $data['auth_url'] = json_encode($auth_url);
        $data['auth_ids'] = json_encode($auth_ids);
        $data['updated_at'] = time();
        $data['created_at'] = strtotime($data['created_at']);
        $result = $this->adminRoleModel->updateResult($data,'id',$data['id']);
        if (!empty($result)){
            return $this->ajax_return(Code::SUCCESS,'role update success');
        }
        return $this->ajax_return(Code::ERROR,'role update error');
    }

    /**
     * todo：删除角色
     * @param Request $request
     * @return JsonResponse
     */
    public function delete(Request $request)
    {
        if ($request->isMethod('get')){
            return $this->ajax_return(Code::METHOD_ERROR,'error');
        }
        $validate = Validator::make($this->post, ['id'=>'required|integer|gt:1' ],['id.gt'=>'Permission denied'] );
        if ($validate->fails()) {
            return $this->ajax_return(Code::ERROR,$validate->errors()->first());
        }
        $result = $this->adminRoleModel->deleteResult('id',$this->post['id']);
        if ($result){
            return $this->ajax_return(Code::SUCCESS,'delete role success');
        }
        return $this->ajax_return(Code::ERROR,'delete role error');
    }
}

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
     * todo：角色列表
     * @param Request $request
     * @return Factory|JsonResponse|View
     */
    public function index(Request $request)
    {
        if ($request->isMethod('get')){
            return $this->ajax_return(Code::METHOD_ERROR,'error');
        }
        $roleLists = $this->roleModel->getResult2();
        foreach ($roleLists as &$item){
            $item->created_at = date("Y-m-d H:i:s",$item->created_at);
            $item->updated_at = date("Y-m-d H:i:s",$item->updated_at);
        }
        $authLists = $this->ruleModel->getAuthList();
        return $this->ajax_return(Code::SUCCESS,'successfully',['role'=>$roleLists,'auth'=>$authLists]);
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
        $validate = Validator::make($this->post, ['status'=>'required|in:1,2', 'auth_ids'=>'required|Array','role_name'=>'required|string'] );
        if ($validate->fails()){
            return $this->ajax_return(Code::ERROR,$validate->errors()->first());
        }
        $authLists = $this->ruleModel->getResult('id',$this->post['auth_ids'],'in',['href']);
        $auth_url = array();
        foreach ($authLists as $item){
            $auth_url[] = strtolower($item->href);
        }
        $this->post['auth_url'] = json_encode($auth_url);
        $this->post['auth_ids'] = json_encode($this->post['auth_ids']);
        $result = $this->roleModel->addResult($this->post);
        if (!empty($result)){
            return $this->ajax_return(Code::SUCCESS,'role save successfully');
        }
        return $this->ajax_return(Code::ERROR,'role save error');
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
            $validate = Validator::make($this->post, ['status'=>'required|in:1,2','id'=>'required|integer|gt:1' ],[  'id.gt'=>'Permission denied']);
            if ($validate->fails()){
                return $this->ajax_return(Code::ERROR,$validate->errors()->first());
            }
            $result = $this->roleModel->updateResult($this->post,'id',$this->post['id']);
            if (!empty($result)){
                return $this->ajax_return(Code::SUCCESS,'role update status successfully');
            }
            return $this->ajax_return(Code::ERROR,'role update status error');
        }
        $validate = Validator::make($this->post, [  'status'=>'required|in:1,2', 'auth_ids'=>'required|Array','role_name'=>'required|string' ]);
        if ($validate->fails()){
            return $this->ajax_return(Code::ERROR,$validate->errors()->first());
        }
        $authLists = $this->ruleModel->getResult('id', $this->post['auth_ids'],'in',['href']);
        $auth_url = array();
        foreach ($authLists as $item){
            $auth_url[] = strtolower($item->href);
        }
        $this->post['auth_url'] = json_encode($auth_url);
        $this->post['auth_ids'] = json_encode($this->post['auth_ids']);
        $this->post['updated_at'] = time();
        $this->post['created_at'] = strtotime($this->post['created_at']);
        $result = $this->roleModel->updateResult($this->post,'id',$this->post['id']);
        if (!empty($result)){
            return $this->ajax_return(Code::SUCCESS,'role update successfully');
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
        $validate = Validator::make($this->post, ['id'=>'required|integer|gt:1'],['id.gt'=>'Permission denied'] );
        if ($validate->fails()) {
            return $this->ajax_return(Code::ERROR,$validate->errors()->first());
        }
        $result = $this->roleModel->deleteResult('id',$this->post['id']);
        if ($result){
            return $this->ajax_return(Code::SUCCESS,'delete role successfully');
        }
        return $this->ajax_return(Code::ERROR,'delete role error');
    }
}

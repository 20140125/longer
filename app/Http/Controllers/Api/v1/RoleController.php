<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * 角色管理
 * Class RoleController
 * @author <fl140125@gmail.com>
 * @package App\Http\Controllers\Api\v1
 */
class RoleController extends BaseController
{
    /**
     * TODO:：角色列表
     * @return JsonResponse
     */
    public function index()
    {
        $this->validatePost(['page'=>'required|integer|gt:0','limit'=>'required|integer|gt:0']);
        $roleLists = $this->roleModel->getResultLists($this->users, $this->post['page'], $this->post['limit']);
        foreach ($roleLists['data'] as &$item) {
            $item->created_at = date("Y-m-d H:i:s", $item->created_at);
            $item->updated_at = date("Y-m-d H:i:s", $item->updated_at);
        }
        $authLists = in_array($this->users->role_id, [1]) ? DB::table('os_rule')->get(['id as key', 'name as label']) : [];
        $data = ['role' => $roleLists, 'auth' => $authLists, 'defaultAuth' => in_array($this->users->role_id, [1]) ? $this->defaultAuth : []];
        return $this->ajaxReturn(Code::SUCCESS, 'successfully', $data);
    }

    /**
     * TODO:：角色保存
     * @param integer status
     * @param array auth_ids
     * @param string role_name
     * @return JsonResponse
     */
    public function save()
    {
        $this->validatePost(['status'=>'required|in:1,2', 'auth_ids'=>'required|Array','role_name'=>'required|string']);
        $authLists = $this->authModel->getResult('id', $this->setDefaultAuth(), 'in', ['href']);
        $auth_url = array();
        foreach ($authLists as $item) {
            $auth_url[] = $item->href;
        }
        $this->post['auth_url'] = str_replace("\\", '', json_encode($auth_url));
        $this->post['auth_ids'] = json_encode($this->setDefaultAuth());
        $result = $this->roleModel->addResult($this->post);
        return !empty($result) ? $this->ajaxReturn(Code::SUCCESS, 'role save successfully') : $this->ajaxReturn(Code::ERROR, 'role save failed');
    }

    /**
     * TODO:：角色更新
     * @param string act 用户行为
     * @param integer status
     * @param integer id
     * @return JsonResponse
     */
    public function update()
    {
        if (!empty($this->post['act']) && $this->post['act'] == 'status') {
            unset($this->post['act']);
            $this->validatePost(['status'=>'required|in:1,2', 'id'=>'required|integer|gt:1' ], ['id.gt'=>'Permission denied']);
            $result = $this->roleModel->updateResult($this->post, 'id', $this->post['id']);
            return !empty($result) ? $this->ajaxReturn(Code::SUCCESS, 'role update status successfully') : $this->ajaxReturn(Code::ERROR, 'role update status error');
        }
        $this->validatePost(['status'=>'required|in:1,2', 'auth_ids'=>'required|Array','role_name'=>'required|string']);
        $auth_ids = array();
        foreach ($this->setDefaultAuth() as $auth_id) {
            if (!in_array($auth_id, $auth_ids)) {
                $auth_ids[] = $auth_id;
            }
        }
        $authLists = $this->authModel->getResult('id', $auth_ids, 'in', ['href']);
        $auth_url = array();
        foreach ($authLists as $item) {
            $auth_url[] = $item->href;
        }
        $this->post['auth_url'] = str_replace("\\", '', json_encode($auth_url));
        $this->post['auth_ids'] = json_encode($auth_ids);
        $this->post['updated_at'] = time();
        $this->post['created_at'] = strtotime($this->post['created_at']);
        $result = $this->roleModel->updateResult($this->post, 'id', $this->post['id']);
        return !empty($result) ? $this->ajaxReturn(Code::SUCCESS, 'role update status successfully') : $this->ajaxReturn(Code::ERROR, 'role update status error');
    }

    /**
     * todo:设置默认的权限
     * @return mixed
     */
    protected function setDefaultAuth()
    {
        foreach ($this->defaultAuth as $auth) {
            if (!in_array($auth, $this->post['auth_ids'])) {
                array_push($this->post['auth_ids'], $auth);
            }
        }
        return $this->post['auth_ids'];
    }

    /**
     * TODO:：删除角色
     * @param integer id
     * @return JsonResponse
     */
    public function delete()
    {
        $this->validatePost(['id'=>'required|integer|gt:1'], ['id.gt'=>'Permission denied']);
        $result = $this->roleModel->deleteResult('id', $this->post['id']);
        return !empty($result) ? $this->ajaxReturn(Code::SUCCESS, 'delete role successfully') : $this->ajaxReturn(Code::ERROR, 'delete role failed');
    }
}

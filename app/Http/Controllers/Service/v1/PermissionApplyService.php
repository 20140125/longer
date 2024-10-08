<?php

namespace App\Http\Controllers\Service\v1;

use App\Http\Controllers\Utils\Code;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class PermissionApplyService
 * @package App\Http\Controllers\Service\v1
 */
class PermissionApplyService extends BaseService
{
    /**
     * @var static $instance
     */
    private static $instance;

    /**
     * @return static
     */
    public static function getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * 获取角色
     * @param $where
     * @param string[] $columns
     * @return Model|Builder|object|null
     */
    public function getPermissionApply($where, array $columns = ['*'])
    {
        return $this->roleModel->getOne($where, $columns);
    }

    /**
     * 获取申请权限列表
     * @param $user
     * @param array $where
     * @param array|int[] $pagination
     * @param array|string[] $order
     * @param array|string[] $columns
     * @return array
     */
    public function getPermissionApplyLists($user, array $where = [], array $pagination = ['page' => 1, 'limit' => 10], array $order = ['order' => 'updated_at', 'direction' => 'desc'], array $columns = ['*'])
    {
        $this->return['lists'] = $this->permissionApplyModel->getLists($user, $where, $pagination, $order, $columns);
        foreach ($this->return['lists']['data'] as &$item) {
            $item->created_at = empty($item->created_at) ? '' : date('Y-m-d H:i:s', $item->created_at);
            $item->updated_at = empty($item->updated_at) ? '' : date('Y-m-d H:i:s', $item->updated_at);
            $item->refresh = $item->expires - (3600 * 24 * 7) <= time();
            $item->expires = empty($item->expires) ? '' : date('Y-m-d H:i:s', $item->expires);
            $item->applyLog = $this->permissionApplyLogModel->getLists(['apply_id' => $item->id]);
        }
        return $this->return;
    }

    /**
     * 添加申请权限
     * @param $form
     * @return array
     */
    public function savePermissionApply($form)
    {
        DB::beginTransaction();
        try {
            $form['expires'] = $form['expires'] ? strtotime($form['expires']) : 0;
            $form['created_at'] = time();
            $form['updated_at'] = time();
            $form['status'] = 2;
            $form['username'] = $this->userModel->getOne(['id' => $form['user_id']], ['username'])->username;
            $result = $this->permissionApplyModel->saveOne($form);
            if (empty($result)) {
                $this->return['code'] = Code::ERROR;
                $this->return['message'] = 'save authorization failed';
                DB::rollBack();
                return $this->return;
            }
            $this->permissionApplyLogModel->saveOne(['apply_id' => $result, 'desc' => '用户申请权限', 'user_name' => $form['username'], 'created_at' => date(time())]);
            $this->return['lists'] = $form;
            DB::commit();
            return $this->return;
        } catch (\Exception $exception) {
            $this->return['code'] = Code::SERVER_ERROR;
            $this->return['message'] = $exception->getMessage();
            DB::rollBack();
            return $this->return;
        }
    }

    /**
     * 申请权限更新
     * @param $form
     * @param $user
     * @return array
     */
    public function updatePermissionApply($form, $user)
    {
        $form = ['id' => $form['id'], 'expires' => strtotime('+ 30 days'), 'updated_at' => time(), 'status' => $form['status']];
        DB::beginTransaction();
        try {
            $result = $this->permissionApplyModel->updateOne(['id' => $form['id']], $form);
            if (empty($result)) {
                $this->return['code'] = Code::ERROR;
                $this->return['message'] = 'update authorization status failed';
                DB::rollBack();
                return $this->return;
            }
            $_roleAuth = $this->getRoleAuth($form['id'], $form['status']);
            /* 审批权限添加角色 */
            $_role = $this->roleModel->getOne(['id' => $_roleAuth['user']->id]);
            if (!empty($_role)) {
                $this->roleModel->updateOne(['id' => $_roleAuth['user']->role_id], $_roleAuth['form']);
                $this->permissionApplyLogModel->saveOne(['apply_id' => $form['id'], 'desc' => '管理员通过申请权限', 'user_name' => $user->username, 'created_at' => date(time())]);
                $this->return['lists'] = $form;
                DB::commit();
                return $this->return;
            }
            $_roleAuth['form']['id'] = $_roleAuth['user']->id;
            $_roleAuth['form']['created_at'] = time();
            $_roleAuth['form']['status'] = 1;
            $_roleAuth['form']['role_name'] = $_roleAuth['user']->username;
            $this->roleModel->saveOne($_roleAuth['form']);
            /* 更新用户角色 */
            $this->userModel->updateOne(['id' => $_roleAuth['form']['id']], ['role_id' => $_roleAuth['form']['id']]);
            $this->permissionApplyLogModel->saveOne(['apply_id' => $form['id'], 'desc' => '用户权限续期成功', 'user_name' => $user->username, 'created_at' => date(time())]);
            $this->return['lists'] = $form;
            DB::commit();
            return $this->return;
        } catch (\Exception $exception) {
            Log::error($exception);
            $this->return['code'] = Code::SERVER_ERROR;
            $this->return['message'] = $exception->getMessage();
            DB::rollBack();
            return $this->return;
        }
    }

    /**
     * 删除权限
     * @param $form
     * @param $user
     * @return array
     */
    public function removePermissionApply($form, $user)
    {
        $form = $this->getRoleAuth($form['id'], $form['status']);
        DB::beginTransaction();
        try {
            $result = $this->permissionApplyModel->updateOne(['id' => $form['request_auth_id']], ['status' => 2, 'created_at' => time(), 'expires' => 0]);
            if (empty($result)) {
                $this->return['code'] = Code::ERROR;
                $this->return['message'] = 'failed';
                DB::rollBack();
                return $this->return;
            }
            $result = $this->roleModel->updateOne(['id' => $form['user']->role_id], $form['form']);
            $this->return['lists'] = $form;
            $this->return['code'] = $result ? Code::SUCCESS : Code::ERROR;
            $this->return['message'] = $result ? 'remove authorization successfully' : 'remove authorization failed';
            $this->permissionApplyLogModel->saveOne(['apply_id' => $form['request_auth_id'], 'desc' => '管理员收回用户权限', 'user_name' => $user->username, 'created_at' => date(time())]);
            DB::commit();
            return $this->return;
        } catch (\Exception $exception) {
            $this->return['code'] = Code::SERVER_ERROR;
            $this->return['message'] = $exception->getMessage();
            DB::rollBack();
            return $this->return;
        }
    }
}

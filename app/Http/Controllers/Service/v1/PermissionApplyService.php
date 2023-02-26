<?php

namespace App\Http\Controllers\Service\v1;

use App\Http\Controllers\Utils\Code;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

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
    public static function getInstance(): PermissionApplyService
    {
        if (!self::$instance instanceof self) {
            self::$instance = new static();
        }
        return self::$instance;
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
    public function getPermissionApplyLists($user, array $where = [], array $pagination = ['page' => 1, 'limit' => 10], array $order = ['order' => 'updated_at', 'direction' => 'desc'], array $columns = ['*']): array
    {
        $this->return['lists'] = $this->permissionApplyModel->getLists($user, $where, $pagination, $order, $columns);
        foreach ($this->return['lists']['data'] as &$item) {
            $item->created_at = empty($item->created_at) ? '' : date('Y-m-d H:i:s', $item->created_at);
            $item->updated_at = empty($item->updated_at) ? '' : date('Y-m-d H:i:s', $item->updated_at);
            $item->refresh = $item->expires - (3600 * 24 * 7) <= time();
            $item->expires = empty($item->expires) ? '' : date('Y-m-d H:i:s', $item->expires);
            $item->applyLog = $this->permissionApplyLogModel->getLists(['apply_id' => $item->id]);
            foreach ($item->applyLog as &$log) {
                $log->created_at = empty($log->created_at) ? '' : date('Y-m-d H:i:s', $log->created_at);
            }
        }
        return $this->return;
    }

    /**
     * 添加申请权限
     * @param $form
     * @return array
     */
    public function savePermissionApply($form): array
    {
        DB::beginTransaction();
        try {
            foreach ($form['href'] as $href) {
                $permission['expires'] = $form['expires'] ? strtotime($form['expires']) : 0;
                $permission['created_at'] = time();
                $permission['updated_at'] = time();
                $permission['status'] = 2;
                $permission['username'] = $this->userModel->getOne(['id' => $form['user_id']], ['username'])->username;
                $permission['href'] = $href;
                $permission['user_id'] = $form['user_id'];
                $permission['desc'] = $form['desc'];
                $result = $this->permissionApplyModel->saveOne($permission);
                if (empty($result)) {
                    $this->return['code'] = Code::ERROR;
                    $this->return['message'] = 'failed permissions request';
                    DB::rollBack();
                    return $this->return;
                }
                $this->permissionApplyLogModel->saveOne([
                    'apply_id' => $result,
                    'desc' => '新用户申请权限',
                    'user_name' => $permission['username'],
                    'created_at' => date(time()),
                    'user_id' =>  $form['user_id']
                ]);
            }
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
    public function updatePermissionApply($form, $user): array
    {
        $permissionExpires = $this->getConfiguration('PermissionExpires');
        $form = ['id' => $form['id'], 'expires' => strtotime("+ $permissionExpires[0] days"), 'updated_at' => time(), 'status' => $form['status']];
        DB::beginTransaction();
        try {
            $permission = $this->permissionApplyModel->getOne(['id' => $form['id']]);
            if (!empty($permission)) {
                $form['expires'] = strtotime("+ $permissionExpires[0] days");
            }
            $result = $this->permissionApplyModel->updateOne(['id' => $form['id']], $form);
            if (empty($result)) {
                $this->return['code'] = Code::ERROR;
                $this->return['message'] = 'update authorization status failed';
                DB::rollBack();
                return $this->return;
            }
            /* 用户权限续期只修改申请权限数据 */
            $_roleAuth = $this->getRoleAuth($form['id'], $form['status']);
            $_role = $this->roleModel->getOne(['id' => $permission->user_id]);
            if (!empty($_role)) {
                /* todo：用户是否授权操作 */
                if (!empty($form['act'])) {
                    $this->roleModel->updateOne(['id' => $_roleAuth['user']->role_id], $_roleAuth['form']);
                }
                $this->permissionApplyLogModel->saveOne([
                    'apply_id' => $form['id'],
                    'desc' => '用户权限续期成功',
                    'user_name' => $user->username,
                    'created_at' => date(time()),
                    'user_id' => $user->id
                ]);
                $this->return['message'] = 'successfully permissions renewal';
                $this->return['lists'] = $form;
                DB::commit();
                return $this->return;
            }
            /* 审批权限添加角色 */
            $_roleAuth['form']['id'] = $_roleAuth['user']->id;
            $_roleAuth['form']['created_at'] = time();
            $_roleAuth['form']['status'] = 1;
            $_roleAuth['form']['role_name'] = $_roleAuth['user']->username. '【系统管理员】';
            $this->roleModel->saveOne($_roleAuth['form']);
            /* 更新用户角色 */
            $this->userModel->updateOne(['id' => $_roleAuth['form']['id']], ['role_id' => $_roleAuth['form']['id']]);
            $this->permissionApplyLogModel->saveOne([
                'apply_id' => $form['id'],
                'desc' => '管理员通过权限申请',
                'user_name' => $user->username,
                'created_at' => date(time()),
                'user_id' => $user->id
            ]);
            $this->return['message'] = 'successfully permissions application';
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
     * 删除权限
     * @param $form
     * @param $user
     * @return array
     */
    public function removePermissionApply($form, $user): array
    {
        $form = $this->getRoleAuth($form['id'], $form['status']);
        DB::beginTransaction();
        try {
            $result = $this->permissionApplyModel->updateOne(['id' => $form['request_auth_id']], ['status' => 2, 'updated_at' => time(), 'expires' => strtotime('-1 seconds')]);
            if (empty($result)) {
                $this->return['code'] = Code::ERROR;
                $this->return['message'] = 'failed reclaimed user permissions';
                DB::rollBack();
                return $this->return;
            }
            if (empty($this->roleModel->updateOne(['id' => $form['user']->role_id], $form['form']))) {
                $this->return['lists'] = $form;
                $this->return['message'] = 'failed reclaimed user permissions';
                $this->return['code'] = Code::ERROR;
                return $this->return;
            }
            $this->permissionApplyLogModel->saveOne([
                'apply_id' => $form['request_auth_id'],
                'desc' => '管理员收回用户权限',
                'user_name' => $user->username,
                'created_at' => date(time()),
                'user_id' => $user->id
            ]);
            $this->return['lists'] = $form;
            $this->return['message'] = 'successfully reclaimed user permissions';
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

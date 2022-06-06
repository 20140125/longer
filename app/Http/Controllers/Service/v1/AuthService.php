<?php

namespace App\Http\Controllers\Service\v1;

use App\Http\Controllers\Utils\Code;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

class AuthService extends BaseService
{
    /**
     * @var static $instance
     */
    private static $instance;

    /**
     * @return AuthService
     */
    public static function getInstance(): AuthService
    {
        if (!self::$instance instanceof self) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * todo:获取权限列表
     * @param $form
     * @param string[] $columns
     * @param bool $getAll
     * @param array $attr
     * @return array
     */
    public function getLists($form, array $columns = ['*'], bool $getAll = false, array $attr = ['key' => 'id', 'ids' => array()]): array
    {
        if ($getAll) {
            $this->return['lists'] = $this->authModel->getLists([], $columns, $attr, ['order' => 'path', 'direction' => 'asc']);
            return $this->return;
        }
        $where = [['status', $form['status'] ?? 1], ['level', '<', $form['level'] ?? 2]];
        /* todo:权限导航栏 */
        if (!empty($form['role_id']) && $form['role_id'] !== 1) {
            $ids = $this->roleModel->getOne(['id' => $form['role_id']], ['auth_ids as ids'])->ids;
            $this->return['lists'] = $this->authModel->getLists($where, $columns, ['key' => 'id', 'ids' => json_decode($ids, true)]);
            return $this->return;
        }
        if (is_numeric($form['id'])) {
            $where = [['pid', (int)$form['id']]];
        }
        /* todo:数据列表 */
        $this->return['lists'] = $this->authModel->getLists($where, $columns, $attr);
        if (is_numeric($form['id'])) {
            foreach ($this->return['lists'] as $auth) {
                $auth->hasChildren = false;
                if ($this->authModel->getOne(['pid' => $auth->id])) {
                    $auth->hasChildren = true;
                }
            }
        }
        return $this->return;
    }

    /**
     * todo:数据添加
     * @param $form
     * @return array
     */
    public function saveAuth($form): array
    {
        $form['api'] = str_replace('/admin/', '/api/v1/', $form['href']);
        $id = $this->authModel->saveOne($form);
        if (!$id) {
            $this->return['code'] = Code::ERROR;
            $this->return['message'] = 'failed';
            return $this->return;
        }
        $parent_result = $this->authModel->getOne(['id' => $form['pid']], ['path']);
        $form['path'] = $id;
        $form['level'] = 0;
        if (!empty($parent_result)) {
            $form['path'] = $parent_result->path . '-' . $id;
            $form['level'] = substr_count($form['path'], '-');
        }
        $result = $this->authModel->updateOne(['id' => $id], $form);
        if (!$result) {
            $this->return['code'] = Code::ERROR;
            $this->return['message'] = 'failed';
            return $this->return;
        }
        $this->return['lists'] = $form;
        return $this->return;
    }

    /**
     * todo:数据更新
     * @param $form
     * @return array
     */
    public function updateAuth($form): array
    {
        if (!empty($form['children']) && count($form['children']) >= 0) {
            unset($form['children']);
        }
        /* todo:修改权限状态 */
        if (!empty($form['act'])) {
            unset($form['act']);
            $result = $this->authModel->updateOne(['id' => (int)$form['id']], $form);
            if (empty($result)) {
                $this->return['code'] = Code::ERROR;
                $this->return['message'] = 'update status failed';
                return $this->return;
            }
            $this->return['lists'] = $form;
            return $this->return;
        }
        /* todo:修改权限 */
        $result = $this->authModel->getOne(['id' => $form['pid']]);
        $form['path'] = !empty($result->path) ? $result->path . '-' . $form['id'] : $form['id'];
        $form['level'] = substr_count($form['path'], '-');
        $result = $this->authModel->updateOne(['id' => $form['id']], $form);
        if (!$result) {
            $this->return['code'] = Code::ERROR;
            $this->return['message'] = 'update auth failed';
            return $this->return;
        }
        $this->return['lists'] = $form;
        return $this->return;
    }
}

<?php

namespace App\Http\Controllers\Service\v1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Utils\Code;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RoleService extends BaseService
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
     * todo:获取角色
     * @param $where
     * @param string[] $columns
     * @return Model|Builder|object|null
     */
    public function getRole($where, array $columns = ['*'])
    {
        return $this->roleModel->getOne($where, $columns);
    }

    /**
     * todo:获取角色列表
     * @param $user
     * @param int[] $pagination
     * @param string[] $order
     * @param string[] $columns
     * @return array
     */
    public function getLists($user, array $pagination = ['page' => 1, 'limit' => 10], array $order = ['order' => 'id', 'direction' => 'desc'], array $columns = ['*'])
    {
        $where = [];
        if (!in_array($user->role_id, [1])) {
            $where[] = ['id', $user->role_id];
        }
        $this->return['lists'] = $this->roleModel->getLists($where, $pagination, $order, false, $columns);
        foreach ($this->return['lists']['data'] as &$item) {
            $item->created_at = date("Y-m-d H:i:s", $item->created_at);
            $item->updated_at = date("Y-m-d H:i:s", $item->updated_at);
        }
        return $this->return;
    }

    /**
     * todo:角色添加
     * @param $form
     * @return array
     */
    public function saveRole($form)
    {
        $form = $this->__initRole($form);
        $form['created_at'] = time();
        $result = $this->roleModel->saveOne($form);
        if (!$result) {
            $this->return['code'] = Code::ERROR;
            $this->return['message'] = 'failed';
        }
        $this->return['lists'] = $form;
        return $this->return;
    }

    /**
     * todo:角色更新
     * @param $form
     * @return array
     */
    public function updateRole($form)
    {
        if (empty($form['act'])) {
            $form = $this->__initRole($form);
            $form['created_at'] = strtotime($form['created_at']);
        }
        if (!empty($form['act'])) unset($form['act']);
        $result = $this->roleModel->updateOne(['id' => $form['id']], $form);
        if (!$result) {
            $this->return['code'] = Code::ERROR;
            $this->return['message'] = 'failed';
        }
        $this->return['lists'] = $form;
        return $this->return;
    }

    /**
     * todo:角色信息初始化
     * @param $form
     * @return mixed
     */
    protected function __initRole($form)
    {
        $_auth_ids = $this->getDefaultAuth($form['auth_ids']);
        $_authLists = $this->authModel->getLists([], ['href'], ['key' => 'id', 'ids' => $_auth_ids]);
        $_auth_url = array();
        foreach ($_authLists as $item) {
            $_auth_url[] = $item->href;
        }
        $form['auth_url'] = str_replace("\\", '', json_encode($_auth_url, JSON_UNESCAPED_UNICODE));
        $form['auth_ids'] = json_encode($_auth_ids, JSON_UNESCAPED_UNICODE);
        $form['updated_at'] = time();
        return $form;
    }
}

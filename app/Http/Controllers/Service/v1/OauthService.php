<?php

namespace App\Http\Controllers\Service\v1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Utils\Code;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;

class OauthService extends BaseService
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
     * todo:获取用户列表
     * @param $user
     * @param array|int[] $pagination
     * @param array|string[] $order
     * @param bool $getAll
     * @param array|string[] $column
     * @return array
     */
    public function getUserLists($user, array $pagination = ['page' => 1, 'limit' => 10], array $order = ['order' => 'id', 'direction' => 'asc'], bool $getAll = false, array $column = ['*'])
    {
        $this->return['lists'] = $this->oauthModel->getLists($user, $pagination, $order, $getAll, $column);
        foreach ($this->return['lists']['data'] as &$item) {
            $item->created_at = date('Y-m-d H:i:s', $item->created_at);
            $item->updated_at = date('Y-m-d H:i:s', $item->updated_at);
        }
        return $this->return;
    }

    /**
     * todo:邮箱账号绑定
     * @param $form
     * @return array
     */
    public function bindEmailAction($form)
    {
        $result = $this->oauthModel->updateOne(['id' => $form['id']], $form);
        if (!$result) {
            $this->return['code'] = Code::ERROR;
            $this->return['message'] = 'bind email failed';
            return $this->return;
        }
        $this->return['lists'] = $form;
        return $this->return;
    }

    /**
     * todo:获取授权用户
     * @param $where
     * @param string[] $columns
     * @return Model|Builder|object|null
     */
    public function getOauth($where, $columns = ['*'])
    {
        return $this->oauthModel->getOne($where, $columns);
    }
}

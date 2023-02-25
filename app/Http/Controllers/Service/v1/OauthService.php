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
    public static function getInstance(): OauthService
    {
        if (!self::$instance instanceof self) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * 获取用户列表
     * @param $user
     * @param array|int[] $pagination
     * @param array|string[] $order
     * @param bool $getAll
     * @param array|string[] $column
     * @param array $form
     * @return array
     */
    public function getUserLists($user, array $pagination = ['page' => 1, 'limit' => 10], array $form = [], array $order = ['order' => 'updated_at', 'direction' => 'desc'], bool $getAll = false, array $column = ['*']): array
    {
        $this->return['lists'] = $this->oauthModel->getLists($user, $pagination, $form, $order, $getAll, $column);
        foreach ($this->return['lists']['data'] as &$item) {
            $item->created_at = date('Y-m-d H:i:s', $item->created_at);
            $item->updated_at = date('Y-m-d H:i:s', $item->updated_at);
        }
        return $this->return;
    }

    /**
     * 邮箱账号绑定
     * @param $form
     * @return array
     */
    public function bindEmailAction($form): array
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

}

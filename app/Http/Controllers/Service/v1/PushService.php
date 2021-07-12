<?php

namespace App\Http\Controllers\Service\v1;

use App\Http\Controllers\Utils\Code;
use App\Jobs\PushProcess;

class PushService extends BaseService
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
     * todo:获取推送列表
     * @param $form
     * @param $user
     * @param array|int[] $pagination
     * @param array|string[] $order
     * @param array|string[] $columns
     * @return array
     */
    public function getPushLists($form, $user, array $pagination = ['page' => 1, 'limit' => 10], array $order = ['order' => 'id', 'direction' => 'desc'], array $columns = ['*'])
    {
        $where = [];
        if (!empty($form['state'])) {
            $where[] = ['state', $form['state']];
        }
        if (!empty($form['status'])) {
            $where[] = ['status', $form['status']];
        }
        if (!in_array($user->role_id, [1])) {
            $where[] = ['uid', $user->uuid];
        }
        $this->return['lists'] = $this->pushModel->getLists($where, $pagination, $order, $columns);
        foreach ($this->return['lists']['data'] as &$item) {
            $item->created_at = date('Y-m-d H:i:s', $item->created_at);
        }
        return $this->return;
    }

    /**
     * todo:站内推送
     * @param $form
     * @return array
     */
    public function savePush($form)
    {
        $form['state'] = Code::WEBSOCKET_STATE[2];
        $form['created_at'] = time();
        $form = intval($form['status']) == 1 ? $this->webPushMessage($form) : $form;
        if ($form['uuid'] != config('app.client_id')) {
            $result = $this->pushModel->saveOne($form);
            if (!$result) {
                $this->return['code'] = Code::ERROR;
                $this->return['message'] = 'web push save failed';
            }
            $this->return['lists'] = $form;
            return $this->return;
        }
        dispatch(new PushProcess($form))->onQueue('webPush')->delay(15);
        $form['username'] = 'admin';
        $form['uuid'] = config('app.client_id').'1';
        $result = $this->pushModel->saveOne($form);
        if (!$result) {
            $this->return['code'] = Code::ERROR;
            $this->return['message'] = 'web push save failed';
        }
        $this->return['lists'] = $form;
        return $this->return;
    }

    /**
     * todo:更新站内通知
     * @param $form
     * @return array
     */
    public function updatePush($form)
    {
        $form['created_at'] = time();
        $form = intval($form['status']) == 1 ? $this->webPushMessage($form) : $form;
        $result = $this->pushModel->updateOne(['id' => $form['id']], $form);
        if (!$result) {
            $this->return['code'] = Code::ERROR;
            $this->return['message'] = 'web push update failed';
        }
        $this->return['lists'] = $form;
        return $this->return;
    }

    /**
     * todo:删除站内通知
     * @param $form
     * @return array
     */
    public function removePush($form)
    {
        $result = $this->pushModel->removeOne(['id' => $form['id']]);
        if (!$result) {
            $this->return['code'] = Code::ERROR;
            $this->return['message'] = 'web push delete failed';
        }
        $this->return['lists'] = $form;
        return $this->return;
    }
}

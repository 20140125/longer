<?php

namespace App\Http\Controllers\Service\v1;

use App\Http\Controllers\Utils\Code;

class LogService extends BaseService
{
    /**
     * @var static $instance
     */
    private static $instance;

    /**
     * @return static
     */
    public static function getInstance(): LogService
    {
        if (!self::$instance instanceof self) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * 获取日志
     * @param $user
     * @param $where
     * @param array $columns
     * @return array
     */
    public function getLog($user, $where, array $columns = ['*']): array
    {
        $where = [['id', $where['id']]];
        if ($user->role_id != 1) {
            $where[] = ['username', $user->username];
        }
        $this->return['lists'] = json_decode(($this->logModel->getOne($where, $columns))->log, true);
        return $this->return;
    }

    /**
     * 獲取日志列表
     * @param $user
     * @param $pagination
     * @return array
     */
    public function getLists($user, $pagination): array
    {
        $where = [];
        if ($user->role_id != 1) {
            $where[] = ['username', $user->username];
        }
        $this->return['lists'] = $this->logModel->getLists($where, $pagination, false, ['order' => 'id', 'direction' => 'desc'], ['id', 'username', 'url', 'ip_address', 'created_at', 'day', 'local', 'log']);
        foreach ($this->return['lists']['data'] as $item) {
            $item->created_at = date("Y-m-d H:i:s", $item->created_at);
            $item->log = json_decode($item->log, true);
        }
        return $this->return;
    }

    /**
     * 删除日志
     * @param $user
     * @param $form
     * @return array
     */
    public function removeLog($user, $form): array
    {
        $where[] = ['id', $form['id']];
        if ($user->role_id != 1) {
            $where[] = ['username', $user->username];
        }
        $result = $this->logModel->removeOne($where);
        if (!$result) {
            $this->return['code'] = Code::ERROR;
            $this->return['message'] = 'Failed remove system log';
            return $this->return;
        }
        $this->return['lists'] = $form;
        return $this->return;
    }
}

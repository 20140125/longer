<?php

namespace App\Http\Controllers\Service\v1;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

class LogService extends BaseService
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
     * todo:获取日志
     * @param $where
     * @param string[] $columns
     * @return Model|Builder|object|null
     */
    public function getLog($where, $columns = ['*'])
    {
        return $this->areaModel->getOne($where, $columns);
    }
    /**
     * todo:保存日志
     * @param $form
     * @return int
     */
    public function saveLog($form)
    {
        $this->return['lists'] = $this->logModel->saveOne($form);
        return $this->return['lists'];
    }

    /**
     * todo:獲取日志列表
     * @param $user
     * @return array
     */
    public function getLists($user, $pagination)
    {
        $where = [];
        if (!in_array($user->role_id, [1])) {
            $where = ['username', $user->username];
        }
        $this->return['lists'] = $this->logModel->getLists($where, $pagination);
        foreach ($this->return['lists']['data'] as &$item) {
            $item->created_at = date("Y-m-d H:i:s", $item->created_at);
            $item->log = json_decode($item->log, true);
        }
        return $this->return;
    }

    /**
     * todo:删除日志
     * @param $user
     * @param $form
     * @return int
     */
    public function removeLog($user,$form)
    {
        $where[] = ['id', $form['id']];
        if (!in_array($user->role_id, [1])) {
            $where[] = ['username', $user->username];
        }
        return $this->logModel->removeOne($where);
    }
}

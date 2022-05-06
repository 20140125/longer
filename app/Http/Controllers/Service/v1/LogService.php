<?php

namespace App\Http\Controllers\Service\v1;

use App\Http\Controllers\Utils\Code;
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
    public function getLog($where, array $columns = ['*'])
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
     * @param $pagination
     * @return array
     */
    public function getLists($user, $pagination)
    {
        $where = [];
        if ($user->role_id != 1) {
            $where[] = ['username', $user->username];
        }
        $this->return['lists'] = $this->logModel->getLists($where, $pagination, false, ['order' => 'id', 'direction' => 'desc'], ['id', 'username', 'url', 'ip_address', 'created_at', 'day', 'local']);
        foreach ($this->return['lists']['data'] as &$item) {
            $item->created_at = date("Y-m-d H:i:s", $item->created_at);
        //    $item->log = json_decode($item->log, true);
        }
        return $this->return;
    }

    /**
     * todo:删除日志
     * @param $user
     * @param $form
     * @return array
     */
    public function removeLog($user, $form)
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

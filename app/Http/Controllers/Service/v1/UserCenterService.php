<?php

namespace App\Http\Controllers\Service\v1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Utils\Code;
use App\Jobs\SyncUserCenterProcess;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class UserCenterService extends BaseService
{
    /**
     * @var static $instance
     */
    private static $instance;

    private $json = ['tags', 'ip_address', 'local'];

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
     * @param $form
     * @return array
     */
    public function getUserInfo($form): array
    {
        $this->return['lists'] = $this->userCenterModel->getOne(['user_id' => $form->id]);
        foreach ($this->json as $item) {
            $this->return['lists']->$item = empty($this->return['lists']->$item) ? [] : json_decode($this->return['lists']->$item, true);
        }
        return $this->return;
    }

    /**
     * 更新用户信息
     * @param $form
     * @param $_user
     * @return array
     */
    public function updateUserInfo($form, $_user): array
    {
        foreach ($this->json as $item) {
            $form[$item] = json_encode($form[$item] ?? [], JSON_UNESCAPED_UNICODE);
        }
        $form['token'] = $_user->remember_token;
        $result = $this->userCenterModel->updateOne(['id' => $form['id']], $form);
        if (!$result) {
            $this->return['code'] = Code::ERROR;
            $this->return['message'] = 'Error update user center';
            return $this->return;
        }
        /* 执行用户相关信息 */
        dispatch(new SyncUserCenterProcess($form))->onQueue('users');
        $this->return['message'] = 'Successfully updated user center';
        $this->return['lists'] = $form;
        return $this->return;
    }
}

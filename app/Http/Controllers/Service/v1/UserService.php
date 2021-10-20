<?php

namespace App\Http\Controllers\Service\v1;

use App\Http\Controllers\Utils\Code;
use App\Jobs\SyncOauthProcess;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;

/**
 * Class UserService
 * @package App\Http\Controllers\Service\v1
 */
class UserService extends BaseService
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
        $this->return['lists'] = $this->userModel->getLists($user, $pagination, $order, $getAll, $column);
        foreach ($this->return['lists']['data'] as &$item) {
            $item->created_at = date('Y-m-d H:i:s', $item->created_at);
            $item->updated_at = date('Y-m-d H:i:s', $item->updated_at);
        }
        $this->return['message'] = 'successfully';
        return $this->return;
    }

    /**
     * todo:用户登录
     * @param $form
     * @return array
     */
    public function loginSYS(&$form)
    {
        $user = $this->userModel->getOne([['email', $form['email']]]);
        if (!empty($form['password'])) {
            /* 用户不存在 */
            if (!$user) {
                $this->return['code'] = Code::NOT_FOUND;
                $this->return['message'] = 'user not exists';
                return $this->return;
            }
            /* 用户被禁用 */
            if ($user->status === 2) {
                $this->return['code'] = Code::FORBIDDEN;
                $this->return['message'] = 'users not allowed login system';
                return $this->return;
            }
        }
        return !empty($form['password']) ? $this->accountLogin($form, $user) : $this->mailLogin($form, $user);
    }

    /**
     * todo：账号密码登录
     * @param $form
     * @param $user
     * @return array
     */
    protected function accountLogin($form, $user)
    {
        /* 密码错误 */
        $password = md5(md5($form['password']) . $user->salt);
        if ($password !== $user->password) {
            $this->return['code'] = Code::ERROR;
            $this->return['message'] = 'password validator failed';
            return $this->return;
        }
        /* 更新用户信息 */
        return $this->updateUsers($form, $user, 'account login system successfully');
    }

    /**
     * todo:邮箱登录
     * @return array
     */
    protected function mailLogin($form, $user)
    {
        return !empty($user) ? $this->updateUsers($form, $user, 'mail login system successfully') : $this->registerUsers($form, 'mail register system successfully');
    }

    /**
     * todo:更新用户信息
     * @param $form
     * @param $user
     * @param $message
     * @return array
     */
    public function updateUsers($form, $user, $message)
    {
        /* 更新用户信息 */
        $form['salt'] = getRoundNum(8, 'all');
        $form['password'] = md5(md5($form['password']) . $form['salt']) === $user->password ? $user->password : md5(md5($form['password']) . $form['salt']);
        /* 自己修改信息时，修改用户标识。管理员修改其他用户时不修改用户标识 */
        $form['remember_token'] = $user->uuid === $form['uuid'] ? encrypt($form['password']) : $form['remember_token'];
        $form['ip_address'] = getServerIp();
        $form['updated_at'] = time();
        $this->userModel->updateOne(['id' => $user->id], $form);
        /* 设置用户标识 */
        $this->setVerifyCode($form['remember_token'], $form['remember_token'], config('app.app_refresh_login_time'));
        $form['uuid'] = $user->uuid ?? '';
        $form['avatar_url'] = $user->avatar_url ?? $this->getUserAvatarImage();
        $form['username'] = $user->username ?? $form['email'];
        $form['socket'] = config('app.socket_url');
        $form['websocket'] = config('app.websocket');
        $form['url'] = config('app.url');
        $this->return['lists'] = $form;
        $this->return['message'] = $message;
        Artisan::call("longer:sync-oauth {$form['remember_token']} longer7f00000108fc00000001");
        return $this->return;
    }

    /**
     * todo:用户注册
     * @param $form
     * @param $message
     * @return array
     */
    protected function registerUsers($form, $message)
    {
        $form['avatar_url'] = $this->getUserAvatarImage();
        $form['salt'] = getRoundNum(8, 'all');
        $form['password'] = md5(md5(getRoundNum(9, 'all')) . $form['salt']);
        $form['remember_token'] = encrypt($form['password']);
        $form['ip_address'] = getServerIp();
        $form['updated_at'] = time();
        $form['created_at'] = time();
        $form['uuid'] = config('app.client_id');
        $form['status'] = 1;
        $form['role_id'] = 2;
        $form['username'] = explode("@", $form['email'])[0];
        $id = $this->userModel->saveOne($form);
        //新用户注册生成client_id
        $this->userModel->updateOne(['id' => $id], [$form['uuid'] => config('app.client_id') . $id]);
        $userCenter = ['u_name' => $form['username'], 'uid' => $id, 'token' => $form['remember_token'], 'notice_status' => 1, 'user_status' => 1];
        $this->userCenterModel->saveOne($userCenter);
        //更新用户画像
        $this->updateUsersAvatarImage();
        //删除redis缓存的验证码，防止恶意登录
        $this->redisClient->del($form['email']);
        // 设置remember_token
        $this->setVerifyCode($form['remember_token'], $form['remember_token'], config('app.app_refresh_login_time'));
        $this->return['lists'] = $form;
        $this->return['message'] = $message;
        return $this->return;
    }

    /**
     * todo:获取用户画像
     * @return int
     */
    private function getUserAvatarImage()
    {
        $users = json_decode($this->redisClient->sMembers(config('app.chat_user_key'))[0], true);
        $avatarUrl = [];
        foreach ($users as $user) {
            if ($user['client_name'] !== 'admin') {
                $avatarUrl[] = $user['client_img'];
            }
        }
        return $avatarUrl[rand(0, count($avatarUrl))];
    }

    /**
     * TODO:更新用户画像
     * @return int
     */
    public function updateUsersAvatarImage()
    {
        $_column = ['username as client_name', 'avatar_url as client_img', 'uuid', 'id'];
        $users = $this->userModel->getLists([], [], [], true, $_column);
        foreach ($users as &$user) {
            $user->centerInfo = $this->userCenterModel->getOne(['uid' => $user->id], ['desc', 'tags', 'ip_address', 'local']);
            $user->id = encrypt($user->id);
        }
        if ($this->redisClient->sMembers(config('app.chat_user_key'))) {
            $this->redisClient->del(config('app.chat_user_key'));
        }
        return $this->redisClient->sAdd(config('app.chat_user_key'), json_encode($users, JSON_UNESCAPED_UNICODE));
    }

    /**
     * todo:获取推送用户列表
     * @return array
     */
    public function getCacheUserList()
    {
        $this->return['lists'] = Cache::get('_users_lists');
        if (empty($this->return['lists'])) {
            $this->return['lists'] = $this->userModel->getLists('', [], [], true, ['id', 'username', 'uuid']);
            Cache::put('_users_lists', $this->return['lists'], Carbon::now()->addHours(2));
        }
        $this->return['message'] = 'successfully';
        return $this->return;
    }

    /**
     * todo:获取用户
     * @param $where
     * @param string[] $columns
     * @return Model|Builder|object|null
     */
    public function getUser($where, array $columns = ['*'])
    {
        return $this->userModel->getOne($where, $columns);
    }
}

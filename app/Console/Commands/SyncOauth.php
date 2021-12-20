<?php

namespace App\Console\Commands;

use App\Http\Controllers\Service\v1\UserService;
use App\Http\Controllers\Utils\RedisClient;
use App\Models\Api\v1\Oauth;
use App\Models\Api\v1\UserCenter;
use App\Models\Api\v1\Users;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncOauth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'longer:sync-oauth {remember_token=default} {uuid=longer7f00000108fc00000001}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'synchronizing user from oauth provider';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Shanghai');
    }

    /**
     * Execute the console command.
     *
     * @return void|boolean
     */
    public function handle()
    {
        $this->syncClientList();
        if ($this->argument('remember_token') === 'default') {
            WebPush('Request remember token is required', $this->argument('uuid'), 'command');
            return false;
        }
        WebPush('Starting synchronizing the oauth list', $this->argument('uuid'), 'command');
        $this->syncOauth();
        WebPush('Finished synchronizing the oauth list', $this->argument('uuid'), 'command');
    }

    /**
     * todo:同步客户端用户列表
     */
    protected function syncClientList()
    {
        WebPush('Starting synchronizing the client user list', $this->argument('uuid'), 'command');
        UserService::getInstance()->updateUsersAvatarImage();
        WebPush('Finished synchronizing the client user list', $this->argument('uuid'), 'command');
    }

    /**
     * todo:同步授权用户信息
     * @return false|void
     */
    protected function syncOauth()
    {
        DB::beginTransaction();
        try {
            $oauth = Oauth::getInstance()->getOne(['remember_token' => $this->argument('remember_token')]) ?? Users::getInstance()->getOne(['remember_token' => $this->argument('remember_token')]);
            if (!$oauth) {
                WebPush('Remember token is invalid', $this->argument('uuid'), 'command');
                return false;
            }
            $users = Users::getInstance()->getOne(['id' => $oauth->uid]);
            if ($users) {
                /* todo：更新用户信息 */
                Users::getInstance()->updateOne(['id' => $oauth->uid], ['remember_token' => $this->argument('remember_token')]);
                WebPush('Successfully updated users： ' . $users->username, $this->argument('uuid'), 'command');
                /* todo：更新用户个人中心 */
                $userCenter = UserCenter::getInstance()->getOne(['uid' => $oauth->uid]);
                if ($userCenter) {
                    UserCenter::getInstance()->updateOne(['id' => $userCenter->id], ['token' => $this->argument('remember_token'), 'u_name' => $oauth->username]);
                    WebPush('Successfully updated users center： ' . $users->username, $this->argument('uuid'), 'command');
                }
                return false;
            }
            $this->saveUsers($oauth);
            $this->saveUserCenter($oauth);
            DB::commit();
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
            DB::rollback();
        }
    }

    /**
     * todo:添加用户
     * @param $oauth
     * @return false|void
     */
    protected function saveUsers($oauth)
    {
        $salt = getRoundNum(8, 'all');
        $userArray = [
            'username'       => $oauth->username,
            'avatar_url'     => $oauth->avatar_url,
            'remember_token' => $oauth->remember_token,
            'email'          => $oauth->email ?? '',
            'salt'           => $salt,
            'password'       => md5(md5('123456789') . $salt),
            'role_id'        => $oauth->role_id,
            'ip_address'     => request()->ip(),
            'created_at'     => time(),
            'updated_at'     => time(),
            'status'         => $oauth->status,
            'phone_number'   => '',
            'uuid'           => ''
        ];
        $userId = Users::getInstance()->saveOne($userArray);
        if (!$userId) {
            WebPush('Failed save users ' . $oauth->username, $this->argument('uuid'), 'command');
            return false;
        }
        Users::getInstance()->updateOne(['id' => $userId], ['uuid' => config('app.client_id') . $userId]);
        Oauth::getInstance()->updateOne(['id' => $oauth->id], ['uid' => $userId, 'uuid' => config('app.client_id').$userId]);
        WebPush('Successfully synchronizing oauth ' . $oauth->username, $this->argument('uuid'), 'command');
    }

    /**
     * todo:添加用户中心记录
     * @param $oauth
     */
    protected function saveUserCenter($oauth)
    {
        $arr = ['u_name' => $oauth->username, 'token' => $oauth->remember_token, 'uid' => $oauth->uid, 'notice_status' => 1, 'user_status' => 1];
        $id = UserCenter::getInstance()->saveOne($arr);
        if ($id) {
            WebPush('Successfully save user center： ' . $oauth->username, $this->argument('uuid'), 'command');
        } else {
            WebPush('Failed save user center： ' . $oauth->username, $this->argument('uuid'), 'command');
        }
    }
}

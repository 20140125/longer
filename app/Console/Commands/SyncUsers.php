<?php

namespace App\Console\Commands;

use App\Http\Controllers\Service\v1\UserService;
use App\Http\Controllers\Utils\RedisClient;
use App\Models\Api\v1\Oauth;
use App\Models\Api\v1\UserCenter;
use App\Models\Api\v1\Users;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'longer:sync-users {remember_token=default} {uuid=longer7f00000108fc00000001}';

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
     *
     */
    public function handle()
    {
        $this->syncUsers();
        /* 更新用户画像 */
        UserService::getInstance()->updateUsersAvatarImage();
    }

    /**
     * todo:同步授权用户信息
     * @return false|void
     */
    protected function syncUsers()
    {
        DB::beginTransaction();
        try {
            $oauth = Oauth::getInstance()->getOne(['remember_token' => $this->argument('remember_token')]);
            if (!$oauth) {
                $this->error('Remember token is invalid');
                return false;
            }
            $users = Users::getInstance()->getOne(['id' => $oauth->uid]);
            if ($users) {
                /* todo：更新用户信息 */
                Users::getInstance()->updateOne(['id' => $oauth->uid], ['remember_token' => $this->argument('remember_token')]);
                $this->info('Successfully updated users： ' . $users->username);
                /* todo：更新用户个人中心 */
                $userCenter = UserCenter::getInstance()->getOne(['uid' => $oauth->uid]);
                if ($userCenter) {
                    UserCenter::getInstance()->updateOne(['id' => $userCenter->id], ['token' => $this->argument('remember_token'), 'u_name' => $oauth->username]);
                    $this->info('Successfully updated users center： ' . $users->username);
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
            $this->error('Failed save users ' . $oauth->username);
            return false;
        }
        Users::getInstance()->updateOne(['id' => $userId], ['uuid' => config('app.client_id') . $userId]);
        Oauth::getInstance()->updateOne(['id' => $oauth->id], ['uid' => $userId, 'uuid' => ['uuid' => config('app.client_id') . $userId]]);
        $this->info('Successfully synchronizing oauth ' . $oauth->username);
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
            $this->info('Successfully save user center： ' . $oauth->username);
        } else {
            $this->error('Failed save user center： ' . $oauth->username);
        }
    }
}

<?php

namespace App\Console\Commands;

use App\Http\Controllers\Service\v1\UserService;
use App\Http\Controllers\Utils\RedisClient;
use App\Models\Api\v1\Oauth;
use App\Models\Api\v1\UserCenter;
use App\Models\Api\v1\Users;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class SyncUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'longer:sync-users {remember_token=default}';

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
        /* 更新Redis Token信息 */
        Artisan::call("longer:sync-remove_redis_token");
    }

    /**
     * 同步授权用户信息
     * @return false|void
     */
    protected function syncUsers()
    {
        DB::beginTransaction();
        try {
            $users = Users::getInstance()->getOne(['remember_token' => $this->argument('remember_token')]);
            if (!$users) {
                $this->error('Remember token is invalid');
                return false;
            }
            /* 更新授权用户信息 */
            if (Oauth::getInstance()->getOne(['uid' => $users->id])) {
                Oauth::getInstance()->updateOne(['uid' => $users->id], ['remember_token' => $this->argument('remember_token')]);
            }
            $this->info('Successfully updated Oauth Token： ' . $users->username);
            /* 更新用户个人中心 */
            UserCenter::getInstance()->updateOne(['uid' => $users->id], ['token' => $this->argument('remember_token'), 'u_name' => $users->username]);
            DB::commit();
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
            DB::rollback();
        }
    }
}

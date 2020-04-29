<?php

namespace App\Console\Commands;

use App\Http\Controllers\Utils\RedisClient;
use App\Models\Users;
use Illuminate\Console\Command;

class SyncUsersCount extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'longer:sync_users_count';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'sync users count';

    protected $redisUtils;
    /**
     * @var Users $usersModel
     */
    protected $usersModel;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->usersModel = Users::getInstance();
        $this->redisUtils = new RedisClient();
    }

    /**
     * TODO:用户列表更新存进redis（脚本用于用户修改头像和名称是缓存数据没有更新）
     * @return int
     */
    public function handle()
    {
        $users = $this->usersModel->getAll([],['username as client_name','avatar_url as client_img','uuid as uid']);
        if ($this->redisUtils->sMembers(config('app.chat_user_key'))) {
            $this->redisUtils->del(config('app.chat_user_key'));
        }
        return $this->redisUtils->sAdd(config('app.chat_user_key'),json_encode($users));
    }
}

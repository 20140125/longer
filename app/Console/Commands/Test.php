<?php

namespace App\Console\Commands;

use App\Models\Api\v1\Oauth;
use App\Models\Api\v1\UserCenter;
use App\Models\Api\v1\Users;
use Illuminate\Console\Command;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'longer:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Testing';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = Users::getInstance()->getLists([], [], [], true);
        foreach ($users as $user) {
            Oauth::getInstance()->updateOne(['uid' => $user->id], ['remember_token' => $user->remember_token]);
            $arr = ['u_name' => $user->username, 'token' => $user->remember_token, 'uid' => $user->id, 'notice_status' => 1, 'user_status' => 1];
            $id = UserCenter::getInstance()->saveOne($arr);
        }
    }
}

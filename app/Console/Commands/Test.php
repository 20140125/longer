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
            $user->remember_token =  encrypt($user->username.time());
            Users::getInstance()->updateOne(['id' => $user->id], ['remember_token' => $user->remember_token]);
            Oauth::getInstance()->updateOne(['uid' => $user->id], ['remember_token' => $user->remember_token]);
            UserCenter::getInstance()->updateOne(['uid' => $user->id], ['token' => $user->remember_token]);
        }
    }
}

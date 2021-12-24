<?php

namespace App\Console\Commands;

use App\Http\Controllers\Utils\RedisClient;
use App\Models\Api\v1\Users;
use Illuminate\Console\Command;

class SyncRedisTokenKey extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'longer:sync-remove_redis_token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'synchronizing remove redis token';
    /**
     * @var RedisClient $redisClient
     */
    protected $redisClient;
    /**
     * @var array $permissionKeys
     */
    protected $permissionKeys;

    protected $usersModel;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->redisClient = RedisClient::getInstance();
        $this->permissionKeys = [config('app.redis_user_key'), config('app.chat_user_key')];
        $this->usersModel = Users::getInstance();
    }

    /**
     * Execute the console command
     */
    public function handle()
    {
        $this->getRedisToken();
    }

    /**
     * todo:获取redisToken
     * @return void
     */
    public function getRedisToken()
    {
        $redisKeys = $this->redisClient->Keys('*');
        foreach ($redisKeys as $item) {
            if (!in_array($item, $this->permissionKeys)) {
                if (empty($this->usersModel->getOne(['remember_token' => $item]))) {
                    $this->redisClient->del($item);
                }
            }
        }
    }
}

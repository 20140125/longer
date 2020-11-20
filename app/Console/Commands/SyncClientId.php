<?php

namespace App\Console\Commands;

use App\Models\Push;
use App\Models\Users;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

/**
 * todo:数据修复用
 * @author <fl140125@gmail.com>
 * Class SyncClientId
 * @package App\Console\Commands
 */
class SyncClientId extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'longer:sync_client_id';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'sync chat client id';
    /**
     * @var Users $userModel
     */
    protected $userModel;
    /**
     * @var Push $pushModel
     */
    protected $pushModel;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->userModel = Users::getInstance();
        $this->pushModel = Push::getInstance();
    }

    /**
     *
     */
    public function handle()
    {
        $this->syncUserUUid();
        sleep(2);
        $this->syncPushUid();
    }

    /**
     * todo:同步用户表的uuid
     */
    protected function syncUserUUid()
    {
        $userUUid = $this->userModel->getAll(['username','uuid','id']);
        Cache::forget('oauthLists');
        Cache::forever('oauthLists', $userUUid);
        $bar = $this->getOutput()->createProgressBar(count($userUUid));
        foreach ($userUUid as $key => $uuid) {
            $this->userModel->updateResult(['uuid'=>config('app.client_id').$uuid->id], 'username', $uuid->username);
            $this->info('当前用户【'.$uuid->username.'】uuid同步成功');
            $bar->advance();
        }
        $bar->finish();
        $this->info('用户数据同步成功');
    }
    /**
     * todo:同步推送用户的uid
     */
    protected function syncPushUid()
    {
        $userUUid = $this->userModel->getAll();
        $bar = $this->getOutput()->createProgressBar(count($userUUid));
        foreach ($userUUid as $uuid) {
            $this->pushModel->updateResult(['uid'=>$uuid->uuid], 'username', $uuid->username);
            $this->info('当前用户【'.$uuid->username.'】uid同步成功');
            $bar->advance();
        }
        $bar->finish();
        $this->info('用户数据同步成功');
    }
}

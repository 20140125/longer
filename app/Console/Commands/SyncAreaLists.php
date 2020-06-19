<?php

namespace App\Console\Commands;

use App\Http\Controllers\Utils\RedisClient;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

/**
 * Class Chat
 * @author <fl140125@gmail.com>
 * @package App\Console\Commands
 */
class SyncAreaLists extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'longer:sync_area';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync area lists';
    /**
     * @var RedisClient
     */
    protected $redisClient;

    protected $areaModel;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->redisClient = new RedisClient();
        $this->areaModel = \App\Models\Area::getInstance();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->getRedisChatMessageLists();
    }

    /**
     * TODO:同步Redis聊天记录到数据库
     */
    protected function getRedisChatMessageLists()
    {
        $result = Cache::get('city');
        if (empty($result)) {
            $result = get_tree($this->areaModel->getAll(),1,'children','parent_id');
            Cache::put('city',$result,7200);
            $this->info('城市列表已经同步到Cache');
        }
    }
}

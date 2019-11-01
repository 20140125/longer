<?php

namespace App\Console\Commands;

use App\Http\Controllers\Utils\RedisClient;
use Illuminate\Console\Command;

/**
 * Class Chat
 * @author <fl140125@gmail.com>
 * @package App\Console\Commands
 */
class Chat extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'longer:chat';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync chat history';
    /**
     * @var RedisClient
     */
    protected $redisClient;

    protected $chatModel;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->redisClient = new RedisClient();
        $this->chatModel = \App\Models\Chat::getInstance();
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
        $keys = $this->redisClient->keys("receive_*");
        if (count($keys)<0) $this->info('not exists');
        foreach ($keys as $key) {
            for ($i = 0;$i<=$this->redisClient->lLen($key);$i++) {
                $item = json_decode($this->redisClient->rPop($key),true);
                $item['content'] = htmlspecialchars_decode($item['content']);
                $this->chatModel->saveResult($item);
            }
            $this->info('successfully');
        }
    }
}

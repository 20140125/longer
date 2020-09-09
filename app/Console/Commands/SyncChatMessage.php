<?php

namespace App\Console\Commands;

use App\Http\Controllers\Utils\RedisClient;
use App\Models\Chat;
use Illuminate\Console\Command;

/**
 * @author <fl140125@gmail.com>
 * Class SyncChatMessage
 * @package App\Console\Commands
 */
class SyncChatMessage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'longer:sync_chat-message';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'sync chat message';
    /**
     * @var Chat $chatModel
     */
    protected $chatModel;
    /**
     * @var RedisClient $redisClient
     */
    protected $redisClient;
    /**
     * @var int[] $pageSize
     */
    protected $pageSize;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->chatModel = Chat::getInstance();
        $this->redisClient = RedisClient::getInstance();
        $this->pageSize = array('page'=>1,'limit'=>20);
    }

    /**
     * todo:聊天记录入库
     */
    public function handle()
    {
        $this->getMessageLists();
    }

    /**
     * todo:执行入库操作
     */
    protected function getMessageLists()
    {
       $keys = $this->getAllRedisKey();
       if (empty($keys)) {
           $this->info('暂无消息需要同步');
           return ;
       }
       foreach ($keys as $key) {
           $lens = $this->redisClient->lLen($key);
           for ($i = 1 ; $i<=$lens;$i++) {
               $chat = json_decode($this->redisClient->rPop($key),true);
               $chatArray = array(
                   'from_client_id' => $chat['from_client_id'],
                   'to_client_id' => $chat['to_client_id'],
                   'room_id' => empty($chat['room_id']) ? '' : $chat['room_id'],
                   'content' => json_encode($chat,JSON_UNESCAPED_UNICODE)
               );
               if ($this->chatModel->saveResult($chatArray)) {
                   $this->info('redis记录入库成功');
               } else {
                   $this->info('redis记录入库失败');
               }
           }
       }
    }
    /**
     * todo:获取redis所有key
     * @return array
     */
    protected function getAllRedisKey ()
    {
        return $this->redisClient->keys('receive_*');
    }
    /**
     * todo:获取redis列表的长度
     * @param $key
     * @return int
     */
    protected function getKeysLen ($key)
    {
        return $this->redisClient->lLen($key);
    }
}

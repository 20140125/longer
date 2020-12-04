<?php

namespace App\Console\Commands;

use App\Http\Controllers\Utils\Code;
use App\Http\Controllers\Utils\RedisClient;
use App\Models\Push;
use App\Models\UserCenter;
use Illuminate\Console\Command;

class SyncSendPush extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'longer:sync-web-push';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Push station information';
    /**
     * @var RedisClient $redisClient
     */
    protected $redisClient;
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
        date_default_timezone_set("Asia/Shanghai");
        $this->redisClient = RedisClient::getInstance();
        $this->pushModel = Push::getInstance();
    }

    /**
     * Execute the console command.
     * @return mixed

     */
    public function handle()
    {
        return $this->getPushLists();
    }

    /**
     * TODO：获取未推送成功列表
     * @return bool|void
     */
    protected function getPushLists()
    {
        $where[] = ['state','<>','successfully'];
        $result = $this->pushModel->getCommandPush($where);
        $bar = $this->output->createProgressBar(count($result));
        foreach ($result as &$item) {
            $userCenter = UserCenter::getInstance()->getResult('u_name', $item->username);
            if (!empty($userCenter) && $userCenter->notice_status == '2') {
                $this->error("　".$item->username .'　已禁用站内信通知');
                return false;
            }
            switch ($item->status) {
                //实时推送
                case 1:
                    if ($this->redisClient->sIsMember(config('app.redis_user_key'), $item->uid)) {
                        try {
                            if (webPush($item->info, $item->uid)) {
                                $item->state = Code::WEBSOCKET_STATE[0];
                                $item->created_at = time();
                                $this->info("　".$item->username .'：站内实时消息推送成功');
                            } else {
                                $item->state = Code::WEBSOCKET_STATE[1];
                                $this->error("　".$item->username .'：站内实时消息推送失败');
                            }
                        } catch (\Exception $e) {
                            $this->error($e->getMessage());
                        }
                    } else {
                        $item->state = Code::WEBSOCKET_STATE[2];
                        $this->warn("　".$item->username . '：已经离线~');
                    }
                    break;
                //定时推送
                case 2:
                    if ($item->created_at<=time()) {
                        if ($this->redisClient->sIsMember(config('app.redis_user_key'), $item->uid)) {
                            try {
                                if (webPush($item->info, $item->uid)) {
                                    $item->state = Code::WEBSOCKET_STATE[0];
                                    $item->created_at = time();
                                    $this->info("　".$item->username .'：站内定时消息推送成功');
                                } else {
                                    $item->state = Code::WEBSOCKET_STATE[1];
                                    $this->error("　".$item->username .'：站内定时消息推送失败');
                                }
                            } catch (\Exception $e) {
                                $this->error($e->getMessage());
                            }
                        } else {
                            $item->state = Code::WEBSOCKET_STATE[2];
                            $this->warn("　".$item->username . '：已经离线~');
                        }
                    } else {
                        $this->info('　未到推送时间');
                    }
                    break;
            }
            $this->pushModel->updateResult(objectToArray($item), 'id', $item->id);
            sleep(0.5);
            $bar->advance();
        }
        $bar->finish();
        $this->info('  successfully!');
    }
}
<?php

namespace App\Console\Commands;

use App\Http\Controllers\Utils\Code;
use App\Http\Controllers\Utils\RedisClient;
use App\Models\Push;
use Illuminate\Console\Command;

class SendPush extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'longer:webPush';

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
        $this->redisClient = new RedisClient();
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
     */
    protected function getPushLists()
    {
        $where[] = ['see','=','0'];
        $result = $this->pushModel->getCommandPush($where);
        $bar = $this->output->createProgressBar(count($result));
        foreach ($result as &$item) {
            switch ($item->status) {
                //实时推送
                case 1:
                    if ($item->username == 'all') {
                        try {
                            if (web_push($item->info)) {
                                $item->state = Code::WebSocketState[0];
                                $this->info("　".$item->username .'　站内实时消息推送所有人成功');
                            } else {
                                $item->state = Code::WebSocketState[1];
                                $this->error("　".$item->username .'　站内实时消息推送所有人失败');
                            }
                        } catch (\ErrorException $e) {
                            $this->error($e->getMessage());
                        }

                    } else if ($this->redisClient->sIsMember(config('app.redis_user_key'), $item->uid)) {
                        try {
                            if (web_push($item->info, $item->username)) {
                                $item->state = Code::WebSocketState[0];
                                $this->info("　".$item->username .'　站内实时消息推送成功');
                            } else {
                                $item->state = Code::WebSocketState[1];
                                $this->error("　".$item->username .'　站内实时消息推送失败');
                            }
                        } catch (\ErrorException $e) {
                            $this->error($e->getMessage());
                        }
                    } else {
                        $item->state = Code::WebSocketState[2];
                        $this->warn("　".$item->username . '已经离线~');
                    }
                    break;
                //定时推送
                case 2:
                    if ($item->created_at<=time()) {
                        if ($item->username == 'all') {
                            try {
                                if (web_push($item->info)) {
                                    $item->state = Code::WebSocketState[0];
                                    $this->info("　".$item->username .'　站内定时消息推送所有人成功');
                                } else {
                                    $item->state = Code::WebSocketState[1];
                                    $this->error("　".$item->username .'　站内定时消息推送所有人失败');
                                }
                            } catch (\ErrorException $e) {
                                $this->error($e->getMessage());
                            }

                        } else if ($this->redisClient->sIsMember(config('app.redis_user_key'), $item->uid)) {
                            try {
                                if (web_push($item->info, $item->username)) {
                                    $item->state = Code::WebSocketState[0];
                                    $this->info("　".$item->username .'　站内定时消息推送成功');
                                } else {
                                    $item->state = Code::WebSocketState[1];
                                    $this->error("　".$item->username .'　站内定时消息推送失败');
                                }
                            } catch (\ErrorException $e) {
                                $this->error($e->getMessage());
                            }
                        } else {
                            $item->state = Code::WebSocketState[2];
                            $this->warn("　".$item->username . '已经离线~');
                        }
                    } else {
                        $this->info('　未到推送时间');
                        return;
                    }
                    break;
            }
            $this->pushModel->updateResult(object_to_array($item),'id',$item->id);
            sleep(0.5);
            $bar->advance();
        }
        $bar->finish();
        $this->info('  successfully!');
    }
}

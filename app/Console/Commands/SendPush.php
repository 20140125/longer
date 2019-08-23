<?php

namespace App\Console\Commands;

use App\Http\Controllers\Utils\Code;
use App\Http\Controllers\Utils\RedisClient;
use App\Models\Push;
use Curl\Curl;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

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
        $this->redisClient = new RedisClient('127.0.0.1');
        $this->pushModel = Push::getInstance();
    }

    /**
     * Execute the console command.
     * @return mixed
     * @throws \ErrorException
     */
    public function handle()
    {
        return $this->getPushLists();
    }

    /**
     * @throws \ErrorException
     */
    protected function getPushLists()
    {
        $where[] = ['state','<>','successfully'];
        $result = $this->pushModel->getCommandPush($where);
        foreach ($result as &$item) {
            switch ($item->status) {
                case 1:
                    if ($item->username == 'all') {
                        if ($this->workerManPush($item->info)) {
                            $item->state = Code::WebSocketState[0];
                            $this->info('广播所有人信息成功');
                        } else {
                            $item->state = Code::WebSocketState[1];
                            $this->error('广播所有人信息失败');
                        }

                    } else if ($this->redisClient->sIsMember(config('app.redis_user_key'), $item->uid)) {
                        if ($this->workerManPush($item->info, $item->username)) {
                            $item->state = Code::WebSocketState[0];
                            $this->info('站内实时消息推送成功');
                        } else {
                            $item->state = Code::WebSocketState[1];
                            $this->error('站内实时消息推送失败');
                        }
                    } else {
                        $item->state = Code::WebSocketState[2];
                        $this->warn($item->username . '已经离线~');
                    }
                    break;
                case 2:
                    if ($item->created_at<=time()) {
                        if ($item->username == 'all') {
                            if ($this->workerManPush($item->info)) {
                                $item->state = Code::WebSocketState[0];
                                $this->info('定时广播所有人信息成功');
                            } else {
                                $item->state = Code::WebSocketState[1];
                                $this->error('定时广播所有人信息失败');
                            }

                        } else if ($this->redisClient->sIsMember(config('app.redis_user_key'), $item->uid)) {
                            if ($this->workerManPush($item->info, $item->username)) {
                                $item->state = Code::WebSocketState[0];
                                $this->info('站内定时消息推送成功');
                            } else {
                                $item->state = Code::WebSocketState[1];
                                $this->error('站内定时消息推送失败');
                            }
                        } else {
                            $item->state = Code::WebSocketState[2];
                            $this->warn($item->username . '已经离线~');
                        }
                    }
                    $this->info('未到推送时间');
                    break;
            }
            $this->info('修改数据库信息');
            $this->pushModel->updateResult(object_to_array($item),'id',$item->id);
            usleep('3000');
            $this->info('休息3000微妙');
        }
    }
    /**
     * TODO：站内信息推送
     * @param $content
     * @param string $uid
     * @return mixed
     * @throws \ErrorException
     */
    protected function workerManPush($content,$uid='')
    {
        // 推送的url地址
        $push_api_url = config('app.push_url');
        $post_data = array(
            "type" => "publish",
            "content" => $content,
            "to" => empty($uid) ? '' : md5($uid),
        );
        $curl = new Curl();
        $curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
        $curl->setOpt(CURLOPT_SSL_VERIFYHOST,false);
        $curl->post($push_api_url,$post_data);
        if ($curl->error) {
            Log::error($curl->errorCode .":".$curl->errorMessage);
            return false;
        }
        return true;
    }
}

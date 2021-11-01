<?php

namespace App\Console\Commands;

use App\Http\Controllers\Utils\Code;
use App\Http\Controllers\Utils\RedisClient;
use App\Models\Api\v1\Push;
use App\Models\Api\v1\UserCenter;
use Illuminate\Console\Command;

class SyncWebPush extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'longer:sync-web_push';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Push notifications on the site';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Shanghai');
    }

    /**
     * Execute the console command.
     * @return void
     */
    public function handle()
    {
        $this->sendWebPusherMessage();
    }

    /**
     * todo；站内推送
     * @return false|void
     */
    protected function sendWebPusherMessage()
    {
        try {
            $lists = Push::getInstance()->getLists([['state', '<>', 'successfully']], true);
            $bar = $this->output->createProgressBar($lists['total']);
            foreach ($lists['data'] as &$item) {
                $userCenter = UserCenter::getInstance()->getOne(['u_name' => $item->username]);
                if (($userCenter->notice_status ?? 1) == 2) {
                    $this->error('【' . $item->username . '】：Disable notification within the station');
                    return false;
                }
                switch ($item->status) {
                    /*todo:立即推送*/
                    case 1:
                        if (!RedisClient::getInstance()->sIsMember(config('app.redis_user_key'), $item->uuid)) {
                            $item->state = Code::WEBSOCKET_STATE[2];
                            $this->warn('【' . $item->username . '】：Already offline~');
                            break;
                        }
                        if (webPush($item->info, $item->uuid)) {
                            $item->state = Code::WEBSOCKET_STATE[0];
                            $item->created_at = time();
                            $this->info('【' . $item->username . '】：Successfully push notification');
                        } else {
                            $item->state = Code::WEBSOCKET_STATE[1];
                            $this->error('【' . $item->username . '】：Failed push notification');
                        }
                        break;
                    /*todo:定时推送*/
                    case 2:
                        if ($item->created_at > time()) {
                            $this->warn('【' . $item->username . '】：Push time yet to come');
                            break;
                        }
                        if (!RedisClient::getInstance()->sIsMember(config('app.redis_user_key'), $item->uuid)) {
                            $item->state = Code::WEBSOCKET_STATE[2];
                            $this->warn('【' . $item->username . '】：Already offline~');
                            break;
                        }
                        if (webPush($item->info, $item->uid)) {
                            $item->state = Code::WEBSOCKET_STATE[0];
                            $item->created_at = time();
                            $this->info('【' . $item->username . '】：Push station timing message successfully');
                        } else {
                            $item->state = Code::WEBSOCKET_STATE[1];
                            $this->error('【' . $item->username . '】：Push station timing message failed');
                        }
                        break;
                }
                usleep(rand(500000, 700000));
                Push::getInstance()->updateOne(['id' => $item->id], (array)$item);
                $bar->advance();
            }
            $bar->finish();
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }
}

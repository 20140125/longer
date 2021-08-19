<?php

namespace App\Jobs;

use App\Http\Controllers\Utils\Code;
use App\Http\Controllers\Utils\RedisClient;
use App\Models\Api\v1\Push;
use App\Models\Api\v1\Users;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SyncPushProcess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var $post
     */
    protected $post;
    /**
     * Create a new job instance.
     * @param $post
     * @return void
     */
    public function __construct($post)
    {
        date_default_timezone_set('Asia/Shanghai');
        $this->post = $post;
    }
    /**
     * Execute the job.
     * todo:站内通知
     * @return void
     */
    public function handle()
    {
        try {
            $_userLists = Cache::get('_user_lists');
            if (empty($_userLists)) {
                $_userLists = Users::getInstance()->getLists('', [], [], true, ['id', 'username','uuid']);
                Cache::put('_user_lists', $_userLists, Carbon::now()->addHours(2));
            }
            $_online_user = RedisClient::getInstance()->sMembers(config('app.redis_user_key'));
            foreach ($_userLists as $item) {
                $this->post['state'] = in_array($item->uuid, $_online_user) ? Code::WEBSOCKET_STATE[0] : Code::WEBSOCKET_STATE[2];
                $this->post['uuid'] = $item->uuid;
                $this->post['username'] = $item->username;
                Push::getInstance()->saveOne($this->post);
            }
        } catch (\Exception $exception) {
            Log::error(json_encode(['code' => Code::SERVER_ERROR, 'message' => $exception->getMessage()]));
        }
    }
}

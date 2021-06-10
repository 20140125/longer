<?php

namespace App\Jobs;

use App\Http\Controllers\Utils\Code;
use App\Http\Controllers\Utils\RedisClient;
use App\Models\Push;
use App\Models\Users;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OauthProcess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    /**
     * @var $post
     */
    protected $post;
    /**
     * @var $redisClient
     */
    protected $redisClient;
    /**
     * @var $pushModel
     */
    protected $pushModel;
    /**
     * Create a new job instance.
     * @param $post
     * @return void
     */
    public function __construct($post)
    {
        $this->post = $post;
        $this->redisClient = RedisClient::getInstance();
        $this->pushModel = Push::getInstance();
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $created_at = (isset($this->post['id']) || !empty($this->post['id'])) ? strtotime($this->post['created_at']) : $this->post['created_at'];
        DB::beginTransaction();
        try {
            $oauthRes = Users::getInstance()->getAll();
            $redisUser = $this->redisClient->sMembers(config('app.redis_user_key'));
            Log::error(json_encode([$redisUser, $oauthRes]));
            foreach ($oauthRes as $item) {
                if (in_array($item->uuid, $redisUser)) {
                    $this->post['state'] = Code::WEBSOCKET_STATE[0];
                }
                $this->post['state'] = Code::WEBSOCKET_STATE[2];
                $this->post['uid'] = $item->uuid;
                $this->post['username'] = $item->username;
                $this->post['created_at'] = $created_at;
                Log::error(json_encode($this->post));
                $rs = $this->pushModel->getResult(array('created_at'=>$created_at,'uid'=>$this->post['uid']));
                if (empty($rs)) {
                    $this->pushModel->addResult($this->post);
                }
            }
            DB::commit();
        } catch (\Exception $exception) {
            Log::error('站内通知对列执行错误：'.$exception);
            DB::rollBack();
        }
    }
}

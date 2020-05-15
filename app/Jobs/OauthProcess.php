<?php

namespace App\Jobs;

use App\Http\Controllers\Utils\Code;
use App\Http\Controllers\Utils\RedisClient;
use App\Models\Users;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OauthProcess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $post;
    /**
     * Create a new job instance.
     * @param $post
     * @return void
     */
    public function __construct($post)
    {
        $this->post = $post;
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
        $redisClient = new RedisClient();
        try{
            $oauthRes = Users::getInstance()->getAll();
            $redisUser = $redisClient->sMembers(config('app.redis_user_key'));
            foreach ($oauthRes as $item) {
                if (in_array($item->uuid,$redisUser)) {
                    $this->post['state'] = Code::WebSocketState[0];
                }
                $this->post['state'] = Code::WebSocketState[2];
                $this->post['uid'] = $item->uuid;
                $this->post['username'] = $item->username;
                $this->post['created_at'] = $created_at;
                $rs = DB::table('os_push')->where(array('created_at'=>$created_at,'uid'=>$this->post['uid']))->first();
                if (empty($rs)) {
                    DB::table('os_push')->insert($this->post);
                }
            }
            DB::commit();
        }catch (\Exception $exception){
            Log::error('站内通知对列执行错误：'.$exception->getMessage());
            DB::rollBack();
        }

    }
}

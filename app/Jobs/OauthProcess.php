<?php

namespace App\Jobs;

use App\Http\Controllers\Utils\RedisClient;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

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
            $oauthRes = DB::table('os_oauth')->get(['username']);
            $redisUser = $redisClient->sMembers(config('app.redis_user_key'));
            foreach ($oauthRes as $item) {
                if (in_array(md5($item->username),$redisUser)) {
                    $this->post['state'] = 'successfully';
                }
                $this->post['state'] = 'offline';
                $this->post['uid'] = md5($item->username);
                $this->post['username'] = $item->username;
                $this->post['created_at'] = $created_at;
                $rs = DB::table('os_push')->where(array('created_at'=>$created_at,'uid'=>$this->post['uid']))->first();
                if (empty($rs)) {
                    DB::table('os_push')->insert($this->post);
                }
            }
            DB::commit();
        }catch (\Exception $exception){
            act_log('站内通知对列执行错误：'.$exception->getMessage());
            DB::rollBack();
        }

    }
}

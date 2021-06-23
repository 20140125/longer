<?php

namespace App\Jobs;

use App\Http\Controllers\Service\v1\UserService;
use App\Http\Controllers\Utils\Code;
use App\Models\Api\v1\Oauth;
use App\Models\Api\v1\Users;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class UserCenterProcess implements ShouldQueue
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
     *
     * @return void
     */
    public function handle()
    {
        try {
            Log::error(json_encode($this->post));
            /* todo: 更新聊天框用户列表信息 */
            UserService::getInstance()->updateUsersAvatarImage();
            /* todo: 更新用户信息 */
//            $_user = array('username' => $this->post['_personal']['u_name'], 'avatar_url' => $this->post['_user']->avatar_url);
//            Users::getInstance()->updateOne(['id' => $this->post['_personal']['uid']], $_user);
//            Oauth::getInstance()->updateOne(['uid' => $this->post['_personal']['uid']], $_user);
        } catch (\Exception $exception) {
            Log::error(json_encode(['code' => Code::SERVER_ERROR, 'message' => $exception->getMessage()]));
        }
    }
}

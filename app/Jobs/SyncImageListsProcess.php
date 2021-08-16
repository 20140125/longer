<?php

namespace App\Jobs;

use App\Http\Controllers\Utils\Code;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class SyncImageListsProcess implements ShouldQueue
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
            is_numeric($this->post['keywords']) ? Artisan::call("longer:sync-spider_image_id {$this->post['keywords']} {$this->post['uuid']}") :
                Artisan::call("longer:sync-spider_image_href {$this->post['keywords']} {$this->post['uuid']}");
        } catch (\Exception $exception) {
            WebPush($exception->getMessage(), $this->post['uuid'], 'command');
            Log::error(json_encode(['code' => Code::SERVER_ERROR, 'message' => $exception]));
        }
    }
}

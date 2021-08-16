<?php

namespace App\Jobs;

use App\Http\Controllers\Utils\Code;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class SyncImageTypeProcess implements ShouldQueue
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
            Artisan::call("longer:sync-spider_image_type {$this->post['keywords']}}");
        } catch (\Exception $exception) {
            WebPush($exception->getMessage(), 'longer7f00000108fc00000001', 'command');
            Log::error(json_encode(['code' => Code::SERVER_ERROR, 'message' => $exception->getMessage()]));
        }
    }
}

<?php

namespace App\Console\Commands;

use App\Models\Api\v1\SooGif;
use App\Models\Api\v1\SooGifType;
use Goutte\Client;
use Illuminate\Console\Command;

class SyncImageSize extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'longer:sync-image_size {id=0} {uuid=longer7f00000108fc00000001}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'synchronize image';

    protected $flag;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->flag = true;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->setFileInfo();
    }
    /**
     * todo:获取文件信息
     */
    protected function setFileInfo()
    {
        global $result;
        try {
            while ($this->flag) {
                $result = SooGif::getInstance()->getOne(['width' => 0]);
                $this->flag = !empty($result);
                if (!empty($result)) {
                    $fileInfo = getimagesize($result->href);
                    if(SooGif::getInstance()->updateOne(['id' => $result->id], ['width' => $fileInfo[0], 'height' => $fileInfo[1]])) {
                        $this->info('Successfully update image size：'.json_encode($result, 256));
                        WebPush('Successfully update image size：'.json_encode($result, 256), $this->argument('uuid'), 'command');
                    } else {
                        $this->error('Failed update image size：'.json_encode($result, 256));
                        WebPush('Failed update image size：'.json_encode($result, 256), $this->argument('uuid'), 'command');
                    }
                }
            }
        } catch (\Exception $exception) {
            $this->error('Failed update image size：'.json_encode($result, 256));
            WebPush('Failed update image size：'.json_encode($result, 256), $this->argument('uuid'), 'command');
            $this->error('error_description：'.$exception->getMessage());
            WebPush('error_description：'.$exception->getMessage(), $this->argument('uuid'), 'command');
            if ($result) {
                SooGif::getInstance()->removeOne(['id' => $result->id]);
            }
            sleep(1);
            $this->setFileInfo();
        }
    }
}

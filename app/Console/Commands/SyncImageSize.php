<?php

namespace App\Console\Commands;

use App\Models\Api\v1\SooGif;
use Illuminate\Console\Command;

class SyncImageSize extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'longer:sync-spider_image_size {id=1} {uuid=longer7f00000108fc00000001}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'synchronizing image size';
    /**
     * @var bool $flag
     */
    protected bool $flag;

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
     *  Execute the console command.
     * @return void
     */
    public function handle(): void
    {
        $this->setFileInfo();
    }

    /**
     * TODO:获取文件信息
     */
    protected function setFileInfo()
    {
        global $result;
        try {
            while ($this->flag) {
                $result = SooGif::getInstance()->getOne(['width' => 0, ['id', '>=', $this->argument('id')]]);
                $this->flag = !empty($result);
                if (!empty($result)) {
                    $fileInfo = getimagesize($result->href);
                    if (SooGif::getInstance()->updateOne(['id' => $result->id], ['width' => $fileInfo[0], 'height' => $fileInfo[1]])) {
                        $this->info('Successfully update image size：' . $result->href);
                        WebPush('Successfully update image size：' . $result->href, $this->argument('uuid'), 'command');
                    } else {
                        $this->error('Failed update image size：' . $result->href);
                        WebPush('Failed update image size：' . $result->href, $this->argument('uuid'), 'command');
                    }
                } else {
                    WebPush('Successfully spider image size', $this->argument('uuid'), 'command');
                }
            }
        } catch (\Exception $exception) {
            $this->error('Failed update image size：' . $result->href);
            WebPush('Failed update image size：' . $result->href, $this->argument('uuid'), 'command');
            $this->error('error_description：' . $exception->getMessage());
            WebPush('error_description：' . $exception->getMessage(), $this->argument('uuid'), 'command');
            if ($result) {
                SooGif::getInstance()->removeOne(['id' => $result->id]);
            }
            usleep(rand(500000, 700000));
            $this->setFileInfo();
        }
    }
}

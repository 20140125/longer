<?php

namespace App\Console\Commands;

use App\Models\Api\v1\SooGif;
use Exception;
use Goutte\Client;
use Illuminate\Console\Command;

class SyncSpiderImageSoogif extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'longer:sync-spider_image_form_soogif {url=https://bj.96weixin.com/material/soogif/761} {uuid=longer7f00000108fc00000001}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $startId;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->startId = 1;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->getImageLists($this->argument('url'));
    }

    /**
     * 获取图片列表
     * @param $url
     * @return void
     */
    protected function getImageLists($url)
    {
        try {
            $client = new Client();
            $promise = $client->request('GET', $url);
            sleep(1);
            preg_match("/\d+/", $promise->filter('.float-page a')->first()->html(), $num);
            if ($promise->filter('.float-page a')->first()->html() && (int)$num[0] > 0) {
                $bar = $this->output->createProgressBar(ceil($num[0] / 30));
                if (stristr($url, '_')) {
                    $this->startId = explode('_', $url)[1];
                }
                $arr = range($this->startId, ceil($num[0] / 30));
                foreach ($arr as $id) {
                    if (stristr($url, '_')) {
                        $url = explode('_', $url)[0];
                    }
                    $href = $url . '_' . $id;
                    $this->info('Current spider link：' . $href);
                    WebPush('Current spider link：' . $href, $this->argument('uuid'), 'command');
                    $promise = $client->request('GET', $href);
                    sleep(1);
                    $promise->filter('.style-item')->each(function ($node) use ($client) {
                        $arr = ['href' => $node->attr('data-img'), 'name' => $node->filter('.item-tools h2')->text()];
                        if (SooGif::getInstance()->getOne(['href' => $arr['href']])) {
                            $this->warn('Image already exists: ' . $arr['href']);
                            webPush('Image already exists: ' . $arr['href'], $this->argument('uuid'), 'command');
                        } else {
                            SooGif::getInstance()->saveOne($arr);
                            $this->info('Successfully save image： ' . $arr['href']);
                            webPush('Successfully save image： ' . $arr['href'], $this->argument('uuid'), 'command');
                        }
                    });
                    $this->info('Successfully spider image url： ' . $href);
                    webPush('Successfully spider image url： ' . $href, $this->argument('uuid'), 'command');
                    $bar->advance();
                }
                $bar->finish();
            }
        } catch (Exception $exception) {
            $this->error($exception->getMessage());
        }
    }
}

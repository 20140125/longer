<?php

namespace App\Console\Commands;

use App\Models\Api\v1\SooGif;
use App\Models\Api\v1\SooGifType;
use Goutte\Client;
use Illuminate\Console\Command;

class SyncSpiderImage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'longer:sync-spider_image_id {id=1} {uuid=longer7f00000108fc00000001}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'synchronizing image from id';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $this->spiderImage($this->argument('id'));
    }

    /**
     * 爬取图片
     * @param $startId
     */
    protected function spiderImage($startId)
    {
        global $currentId;
        try {
            $result = SooGifType::getInstance()->getLists([['id', '>=', $startId]]);
            $bar = $this->output->createProgressBar(count($result));
            $client = new Client();
            foreach ($result as $item) {
                $currentId = $item->id;
                $promise = $client->request('GET', $item->href);
                $this->info("\r\ncurrent spider image url：" . $item->href);
                sleep(1);
                $item->source === 1 ? $this->spiderPkImage($promise, $item, $client, $bar) : $this->spiderFbqImage($promise, $item, $client, $bar);
            }
            $bar->finish();
        } catch (\Exception $exception) {
            $this->error($exception->getMessage() . ' currentId：' . $currentId);
            $this->spiderImage($currentId);
        }
    }

    /**
     * 获取图片
     * @param $promise
     * @param $item
     * @param $client
     * @param $bar
     * @return void
     */
    private function spiderFbqImage($promise, $item, $client, $bar)
    {
        try {
            $promise->filter('.bqpp .bqppdiv1')->each(function ($node) use ($client) {
                $href = str_replace('http', 'https', $node->filter('img')->attr('data-original'));
                if (SooGif::getInstance()->getOne(['href' => $href])) {
                    $this->warn('image already exists: ' . $href);
                } else {
                    SooGif::getInstance()->saveOne(['href' => $href,'name' => mb_substr($node->text(), 0, 50)]);
                    $this->info('successfully save image： ' . $href);
                }
            });
            $this->info('successfully spider image url： ' . $item->href);
            $bar->advance();
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }

    /**
     * 爬取图片
     * @param $promise
     * @param $item
     * @param $client
     * @param $bar
     * @return void
     */
    private function spiderPkImage($promise, $item, $client, $bar)
    {
        try {
            $promise->filter('.artile_des img')->each(function ($node) use ($client, $item) {
                $href = $node->attr('src');
                if (SooGif::getInstance()->getOne(['href' => $href])) {
                    $this->warn('image already exists: ' . $href);
                } else {
                    SooGif::getInstance()->saveOne(['href' => $href,'name' => $item->name]);
                    $this->info('successfully save image： ' . $href);
                }
            });
            $bar->advance();
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }
}

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
     * @var string $baseUrl
     */
    protected $baseUrl;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->baseUrl = 'https://www.fabiaoqing.com';
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $startId = $this->argument('id');
        $this->spiderImage($startId);
    }

    /**
     * todo:爬取图片
     * @param $startId
     */
    protected function spiderImage($startId)
    {
        global $currentId;
        try {
            $result = SooGifType::getInstance()->getLists([['id' ,'>=', $startId]]);
            $bar = $this->output->createProgressBar(count($result));
            $client = new Client();
            foreach ($result as $item) {
                $currentId = $item->id;
                $this->info('current spider image url：' .$item->href);
                WebPush('current spider image url：' .$item->href, $this->argument('uuid'), 'command');
                $promise = $client->request('GET', $item->href);
                sleep(1);
                $promise->filter('.bqpp .bqppdiv1')->each(function($node) use ($client) {
                    if (SooGif::getInstance()->getOne(['href' => str_replace('http', 'https', $node->filter('img')->attr('data-original'))])) {
                        $this->warn('image already exists: '. str_replace('http', 'https', $node->filter('img')->attr('data-original')));
                        WebPush('image already exists: '. str_replace('http', 'https', $node->filter('img')->attr('data-original')), $this->argument('uuid'), 'command');
                    } else {
                        SooGif::getInstance()->saveOne([
                            'href' => str_replace('http', 'https', $node->filter('img')->attr('data-original')),
                            'name' => mb_substr($node->text(), 0, 50)
                        ]);
                        $this->info('successfully save image： '. $node->filter('img')->attr('data-original'));
                        WebPush('successfully save image： '. $node->filter('img')->attr('data-original'), $this->argument('uuid'), 'command');
                    }
                });
                $this->info('successfully spider image url： ' .$item->href);
                WebPush('successfully spider image url： ' .$item->href, $this->argument('uuid'), 'command');
                $bar->advance();
            }
            $bar->finish();
        } catch (\Exception $exception) {
            $this->error($exception->getMessage().' currentId：'.$currentId);
            $this->spiderImage($currentId);
        }
    }
}

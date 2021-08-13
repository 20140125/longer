<?php

namespace App\Console\Commands;

use App\Models\Api\v1\SooGif;
use App\Models\Api\v1\SooGifType;
use Goutte\Client;
use Illuminate\Console\Command;

class SyncSpiderImageService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'longer:sync-spider_image { href=https://www.fabiaoqing.com/bqb/detail/id/54557.html }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'synchronize image service';
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
        $href = $this->argument('href');
        $this->spiderImage($href);
    }

    /**
     * todo:爬取图片
     * @param $startId
     */
    protected function spiderImage($href)
    {
        try {
            $client = new Client();
            $this->info('current spider image url：' .$href);
            $promise = $client->request('GET', $href);
            sleep(1);
            $promise->filter('.bqpp .bqppdiv1')->each(function($node) use ($client) {
                if (SooGif::getInstance()->getOne(['href' => str_replace('http', 'https', $node->filter('img')->attr('data-original'))])) {
                    $this->warn('image already exists: '. str_replace('http', 'https', $node->filter('img')->attr('data-original')));
                } else {
                    SooGif::getInstance()->saveOne([
                        'href' => str_replace('http', 'https', $node->filter('img')->attr('data-original')),
                        'name' => mb_substr($node->text(), 0, 50)
                    ]);
                    $this->info('successfully save image： '. $node->filter('img')->attr('data-original'));
                }
            });
            $this->info('successfully spider image url： ' .$href);
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }
}

<?php

namespace App\Console\Commands;

use App\Models\Api\v1\SooGif;
use Goutte\Client;
use Illuminate\Console\Command;

class SyncSpiderImageService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'longer:sync-spider_image_url {href=https://www.fabiaoqing.com/bqb/detail/id/54557.html} {uuid=longer7f00000108fc00000001}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'synchronizing image from url';
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
     * 爬取图片
     * @param $startId
     */
    protected function spiderImage($href)
    {
        try {
            $client = new Client();
            $this->info('current spider image url：' . $href);
            WebPush('current spider image url：' . $href, $this->argument('uuid'), 'command');
            $promise = $client->request('GET', $href);
            sleep(1);
            $promise->filter('.bqpp .bqppdiv1')->each(function ($node) use ($client) {
                if (SooGif::getInstance()->getOne(['href' => str_replace('http', 'https', $node->filter('img')->attr('data-original'))])) {
                    $this->warn('Image already exists: ' . str_replace('http', 'https', $node->filter('img')->attr('data-original')));
                    WebPush('Image already exists: ' . str_replace('http', 'https', $node->filter('img')->attr('data-original')), $this->argument('uuid'), 'command');
                } else {
                    SooGif::getInstance()->saveOne([
                        'href' => str_replace('http', 'https', $node->filter('img')->attr('data-original')),
                        'name' => mb_substr($node->text(), 0, 50)
                    ]);
                    $this->info('Successfully save image： ' . $node->filter('img')->attr('data-original'));
                    WebPush('Successfully save image： ' . $node->filter('img')->attr('data-original'), 'command');
                }
            });
            $this->info('Successfully spider image url： ' . $href);
            WebPush('Successfully spider image url：' . $href, $this->argument('uuid'), 'command');
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }
}

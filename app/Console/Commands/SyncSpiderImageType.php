<?php

namespace App\Console\Commands;

use App\Models\Api\v1\SooGifType;
use Goutte\Client;
use Illuminate\Console\Command;

class SyncSpiderImageType extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'longer:sync-spider_image_type {type=hot} {uuid=longer7f00000108fc00000001}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'synchronize image type';

    /**
     * Create a new command instance.
     * @return void
     */
    /**
     * @var int $startPage
     */
    protected $startPage;
    /**
     * @var string $baseUrl
     */
    protected $baseUrl;

    public function __construct()
    {
        parent::__construct();
        $this->startPage = 1;
        $this->baseUrl = 'https://www.fabiaoqing.com';
    }

    /**
     * Execute the console command.
     * @return void
     */
    public function handle()
    {
        $this->getImageType();
    }

    /**
     * todo:获取图片类型
     */
    protected function getImageType()
    {
        try {
            $client = new Client();
            $promise = $client->request('GET', sprintf($this->baseUrl.'/bqb/lists/type/%s.html', $this->argument('type')));
            $pageSize = $promise->filter('#mobilepage')->text();
            $pageRange = range($this->startPage, explode('/', $pageSize)[1]);
            $bar = $this->output->createProgressBar(explode('/', $pageSize)[1]);
            foreach ($pageRange as $item) {
                $url = sprintf('https://www.fabiaoqing.com/bqb/lists/type/%s/page/%s.html', $this->argument('type'), $item);
                $this->info('current spider image url：' .$url);
                webPush('current spider image url：' .$url, $this->argument('uuid'), 'command');
                sleep(1);
                $hotPromise = $client->request('GET', $url);
                $hotPromise->filter('.bqba')->each(function ($node) use ($client) {
                    if (SooGifType::getInstance()->getOne(['href' => $this->baseUrl.$node->attr('href')])) {
                        $this->warn('link address already exists: '. $this->baseUrl.$node->attr('href'));
                        webPush('link address already exists: '. $this->baseUrl.$node->attr('href'), $this->argument('uuid'), 'command');
                    } else {
                        SooGifType::getInstance()->saveOne(['href' => $this->baseUrl.$node->attr('href'), 'name' => $node->filter('.header')->text() ]);
                        $this->info('successfully save link address： '. $this->baseUrl.$node->attr('href'));
                        webPush('successfully save link address： '. $this->baseUrl.$node->attr('href'), $this->argument('uuid'), 'command');
                    }
                });
                $this->info('successfully spider image url： ' .$url);
                webPush('successfully spider image url： ' .$url, $this->argument('uuid'), 'command');
                $bar->advance();
                $this->info("\r\n");
            }
            $bar->finish();
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }
}

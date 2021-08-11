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
    protected $signature = 'longer:sync-spider_image_type';

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
    protected $startPage;
    protected $baseUrl;
    public function __construct()
    {
        parent::__construct();
        $this->startPage = 1;
        $this->baseUrl = 'http://www.fabiaoqing.com';
    }

    /**
     * Execute the console command.
     * @return void
     */
    public function handle()
    {
        $this->getImageType();
    }

    protected function getImageType()
    {
        try {
            $client = new Client();
            $promise = $client->request('GET', $this->baseUrl.'/bqb/lists');
            $pageSize = $promise->filter('#mobilepage')->text();
            $pageRange = range($this->startPage, explode('/', $pageSize)[1]);
            $bar = $this->output->createProgressBar(explode('/', $pageSize)[1]);
            foreach ($pageRange as $item) {
                $url = sprintf('https://fabiaoqing.com/bqb/lists/type/hot/page/%s.html', $item);
                $this->info("\r\ncurrent spider image url: " .$url);
                sleep(1);
                $hotPromise = $client->request('GET', $url);
                $hotPromise->filter('.bqba')->each(function ($node) use ($client) {
                    $this->info($node->attr('href'));
                    $this->info($node->filter('.header')->text());
                    if (SooGifType::getInstance()->getOne(['href' => $this->baseUrl.$node->attr('href')])) {
                        $this->warn('link address is already '. $this->baseUrl.$node->attr('href'));
                    } else {
                        SooGifType::getInstance()->saveOne(['href' => $this->baseUrl.$node->attr('href'), 'name' => $node->filter('.header')->text(), 'pid' => 105 ]);
                        $this->info('successfully add link address '. $this->baseUrl.$node->attr('href'));
                    }
                });
                $this->info('successfully spider image url: ' .$url);
                $bar->advance();
            }
            $bar->finish();
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }
}

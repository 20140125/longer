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
    protected $signature = 'longer:sync-spider_image_type {source=pk} {type=hot} {page=1} {uuid=longer7f00000108fc00000001}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'synchronizing image type';
    /**
     * @var int $startPage
     */
    protected int $startPage;
    /**
     * @var string $baseUrl
     */
    protected string $fbqURL = 'https://www.fabiaoqing.com';
    /**
     * @var string $pkURL
     */
    protected string $pkURL = 'https://www.pkdoutu.com/article/list/';
    /**
     * @var string
     */
    protected string $image = 'https://img.pkdoutu.com/';

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
     * @return void
     */
    public function handle(): void
    {
        $this->argument('source') === 'pk' ? $this->getPkImageType() : $this->getFbqImageType();
    }

    /**
     * todo:获取图片类型
     */
    protected function getFbqImageType()
    {
        $type = $this->argument('type');
        try {
            $client = new Client();
            $promise = $client->request('GET', $this->fbqURL . "/bqb/lists/type/$type.html");
            $pageSize = (int)$promise->filter('.pagination .item')->eq(count($promise->filter('.pagination .item')) - 3)->text();
            $bar = $this->output->createProgressBar($pageSize - $this->argument('page'));
            foreach (range($this->argument('page'), $pageSize) as $page) {
                $url = sprintf('%s/%s', $this->fbqURL, "/bqb/lists/type/$type/page/$page.html");
                $this->info("\r\ncurrent spider image url：" . $url);
                sleep(1);
                $hotPromise = $client->request('GET', $url);
                $hotPromise->filter('.bqba')->each(function ($node) use ($client) {
                    if (SooGifType::getInstance()->getOne(['href' => $this->fbqURL . $node->attr('href')])) {
                        $this->warn('link address already exists: ' . $this->fbqURL . $node->attr('href'));
                    } else {
                        SooGifType::getInstance()->saveOne(['href' => $this->fbqURL . $node->attr('href'), 'name' => $node->filter('.header')->text()]);
                        $this->info('successfully save link address： ' . $this->fbqURL . $node->attr('href'));
                    }
                });
                $this->info('successfully spider image url： ' . $url);
                $bar->advance();
            }
            $bar->finish();
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }

    /**
     * todo:同步pkdoutu套图
     * @return void
     */
    protected function getPkImageType()
    {
        try {
            $client = new Client();
            $promise = $client->request('GET', $this->pkURL);
            $pageSize = (int)$promise->filter('.pagination li .page-link')->eq(count($promise->filter('.pagination li .page-link')) - 2)->text();
            $bar = $this->output->createProgressBar($pageSize - $this->argument('page'));
            foreach (range($this->argument('page'), $pageSize) as $page) {
                $lists = $client->request('GET', sprintf('%s%s',   $this->pkURL, '?page='.$page));
                $this->info(sprintf('%s%s%s', "\r\ncurrent spider image url：",  $this->pkURL, '?page='.$page));
                $lists->filter('.center-wrap .list-group-item')->each(function ($node) use ($client) {
                    if (SooGifType::getInstance()->getOne(['href' => $node->attr('href')])) {
                        $this->warn('link address already exists: ' . $node->attr('href'));
                    } else if (!empty($node->attr('href'))) {
                        SooGifType::getInstance()->saveOne(['href' => $node->attr('href'), 'name' => mb_substr($node->filter('.random_title')->text(), 0, 100)]);
                        $this->info('successfully save link address： ' . $node->attr('href'));
                    }
                });
                sleep(2);
                $bar->advance();
            }
            $bar->finish();
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }
}

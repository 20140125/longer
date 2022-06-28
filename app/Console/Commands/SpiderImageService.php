<?php

namespace App\Console\Commands;

use App\Models\Api\v1\SooGif;
use Exception;
use Goutte\Client;
use Illuminate\Console\Command;

class SpiderImageService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'longer:sync-spider_image {url=https://www.pkdoutu.com/photo/list/} {uuid=longer7f00000108fc00000001}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'sync spider image';
    /**
     * @var string
     */
    protected string $href = 'https://www.pkdoutu.com/';
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
     *
     * @return void
     */
    public function handle(): void
    {
        $this->getImageLists($this->argument('url'));
    }

    /**
     * todo:获取图片信息
     * @param $url
     * @return void
     */
    protected function getImageLists($url): void
    {
        try {
            $client = new Client();
            $promise = $client->request('GET', $url);
            $pageSize = $promise->filter('.pagination li .page-link')->eq(count($promise->filter('.pagination li .page-link')) - 2)->text();
            $bar = $this->output->createProgressBar($pageSize);
            foreach (range(1, $pageSize) as $page) {
                $lists = $client->request('GET', sprintf('%s%s',  $url, '?page='.$page));
                $this->info(sprintf('%s%s%s', "\r\n爬取地址：",  $url, 'photo/list/?page=1'));
                $lists->filter('.page-content a')->each(function ($node, $index) use ($client) {
                    $arr = [
                        'href' => $node->filter('.img-responsive')->attr('data-original'),
                        'name' => $node->filter('.img-responsive')->attr('alt') ?? '斗图啦~',
                    ];
                    if (SooGif::getInstance()->getOne(['href' => $arr['href']])) {
                        $this->warn('Image already exists: ' . $arr['href']);
                    } else {
                        SooGif::getInstance()->saveOne($arr);
                        $this->info('successfully save image： ' . $arr['href']);
                    }
                });
                sleep(2);
                $bar->advance();
            }
            $bar->finish();
        } catch (Exception $exception) {
            $this->error($exception->getMessage());
        }
    }
}

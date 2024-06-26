<?php

namespace App\Console\Commands;

use App\Http\Controllers\Service\v1\SystemConfigService;
use App\Models\Api\v1\SooGif;
use App\Models\Api\v1\SystemConfig;
use Goutte\Client;
use Illuminate\Console\Command;

class SyncSpiderImageTag extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    /* https://www.fabiaoqing.com/tag/detail/id/?.html ? ID变量  */
    protected $signature = 'longer:sync-spider_image_tag_url {url=https://fabiaoqing.com/biaoqing/lists/page/1.html} {uuid=longer7f00000108fc00000001}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'synchronizing image list from tags';
    /**
     * @var int $startPage
     */
    protected int $startPage;
    /**
     * @var string $source
     */
    protected string $source;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->startPage = 1;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle(): void
    {
        $spiderTagId = SystemConfigService::getInstance()->getConfiguration('SpiderTagId', 'ImageBed');
        $url = $this->argument('url');
        if ($url == 'https://www.fabiaoqing.com/tag/detail/id/?.html') {
            $ids = range($spiderTagId[0], $spiderTagId[1]);
            $this->source = 'tags';
            foreach ($ids as $id) {
                $this->getImageLists(str_replace('?', $id, $url));
            }
        } else {
            $this->source = 'lists';
            $this->getImageLists($url);
        }
    }

    /**
     * 获取图片
     * @param $url
     */
    protected function getImageLists($url)
    {
        try {
            if (stristr($url, 'page')) {
                $this->startPage = intval(explode('.', substr($url, strrpos($url, '/') + 1))[0]);
                $url = substr($url, 0, strrpos($url, '/') - 5) . '.html';
            }
            $client = new Client();
            $promise = $client->request('GET', $url);
            $pageSize = $promise->filter('#mobilepage')->text();
            $totalPage = (intval(explode('/', $pageSize)[1]) >= 100 && $this->source === 'tags') ? 1 : explode('/', $pageSize)[1];
            $pageRange = range($this->startPage, $totalPage);
            $bar = $this->output->createProgressBar($totalPage);
            foreach ($pageRange as $item) {
                if (stristr($url, 'page')) {
                    $url = substr($url, 0, strrpos($url, '/') - 5) . '.html';
                }
                $url = str_replace('.html', '/page/' . $item . '.html', $url);
                $this->info('current spider image url：' . $url);
                webPush('current spider image url：' . $url, $this->argument('uuid'), 'command');
                sleep(1);
                $hotPromise = $client->request('GET', $url);
                $hotPromise->filter('.segment .tagbqppdiv')->each(function ($node) use ($client) {
                    $arr = [
                        'href' => str_replace('http', 'https', $node->filter('img')->attr('data-original')),
                        'name' => mb_substr($node->filter('img')->attr('alt'), 0, 50)
                    ];
                    if (SooGif::getInstance()->getOne(['href' => $arr['href']])) {
                        $this->warn('Image already exists: ' . $arr['href']);
                        webPush('Image already exists: ' . $arr['href'], $this->argument('uuid'), 'command');
                    } else {
                        SooGif::getInstance()->saveOne($arr);
                        $this->info('Successfully save image： ' . $arr['href']);
                        webPush('Successfully save image： ' . $arr['href'], $this->argument('uuid'), 'command');
                    }
                });
                $this->info('Successfully spider image url： ' . $url);
                webPush('Successfully spider image url： ' . $url, $this->argument('uuid'), 'command');
                $bar->advance();
            }
            $bar->finish();
        } catch (\Exception $exception) {
            webPush($exception->getMessage() . $url, $this->argument('uuid'), 'command');
            $this->error($exception->getMessage());
        }
    }
}

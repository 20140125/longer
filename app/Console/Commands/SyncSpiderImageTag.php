<?php

namespace App\Console\Commands;

use App\Models\Api\v1\SooGif;
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
    protected $signature = 'longer:sync-spider_image_tag_url {url=https://www.fabiaoqing.com/biaoqing/lists/page/1.html} {uuid=longer7f00000108fc00000001}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'synchronizing image list from tags';
    /**
     * @var int $startPage
     */
    protected $startPage;

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
    public function handle()
    {
        $url = $this->argument('url');
        $this->getImageLists($url);
    }

    /**
     * todo:获取图片
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
            $pageRange = range($this->startPage, explode('/', $pageSize)[1]);
            $bar = $this->output->createProgressBar(explode('/', $pageSize)[1]);
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

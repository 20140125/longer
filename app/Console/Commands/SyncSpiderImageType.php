<?php

namespace App\Console\Commands;

use Goutte\Client;
use Illuminate\Console\Command;

class SyncSpiderImageType extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'longer:sync-spider_image_type {uuid=longer7f00000108fc00000001}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'synchronizing image type';

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

    /**
     * @var array $data
     */
    protected $data;
    /**
     * @var string[] $dirName
     */
    protected $dirName;

    public function __construct()
    {
        parent::__construct();
        $this->startPage = 1;
        $this->baseUrl = 'https://www.fabiaoqing.com';
        $this->data = [];
        $this->dirName = ['emoji', 'liaomei', 'qunliao', 'doutu', 'duiren', 'hot'];
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
     * 获取图片类型
     */
    protected function getImageType()
    {
        try {
            $client = new Client();
            foreach ($this->dirName as $dir) {
                $promise = $client->request('GET',  "$this->baseUrl/bqb/lists/type/$dir.html");
                $pageSize = $promise->filter('#mobilepage')->text();
                $pageRange = range($this->startPage, explode('/', $pageSize)[1]);
                $bar = $this->output->createProgressBar(explode('/', $pageSize)[1]);
                foreach ($pageRange as $item) {
                    $url = "$this->baseUrl/bqb/lists/type/$dir/page/$item.html";
                    $this->info("current spider image url：$url");
                    sleep(1);
                    $hotPromise = $client->request('GET', $url);
                    $hotPromise->filter('.bqba')->each(function ($node) use ($client) {
                        $content = ['href' => $this->baseUrl . $node->attr('href'), 'name' => $node->filter('.header')->text()];
                        $this->data[] = $content;
                        $this->info(json_encode($content));
                    });
                    $this->info("successfully spider image url: $url");
                    $bar->advance();
                }
                // 1.打开文件 不存在则创建
                $fileName = public_path("json/$dir");
                if (!file_exists($fileName)) {
                    mkdir($fileName, 0777, true);
                }
                // 2.写入内容之前删除已有的文件
                $imageUrlPath = $fileName.DIRECTORY_SEPARATOR.'image.json';
                if (file_exists($imageUrlPath)) {
                    removeFiles($imageUrlPath);
                }
                // 3.写入内容
                writeFile($imageUrlPath, json_encode($this->data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                $bar->finish();
            }
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }
}

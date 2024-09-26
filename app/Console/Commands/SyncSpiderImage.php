<?php

namespace App\Console\Commands;

use Goutte\Client;
use Illuminate\Console\Command;

class SyncSpiderImage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'longer:sync-spider_image {uuid=longer7f00000108fc00000001}';

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
     * @var array $data
     */
    protected $data;
    /**
     * @var string[] $dirName
     */
    protected $dirName;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->baseUrl = 'https://www.fabiaoqing.com';
        $this->data = [];
        $this->dirName = ['emoji', 'liaomei', 'qunliao', 'doutu', 'duiren', 'hot'];
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->spiderImage();
    }

    /**
     * 爬取图片
     */
    protected function spiderImage()
    {
        try {
            $client = new Client();
            foreach ($this->dirName as $dir) {
                $filePath = public_path("json/$dir/image.json");
                $this->info("current spider dir：$filePath");
                $result = json_decode(getFileContent($filePath));
                if (!$result) continue;
                $bar = $this->output->createProgressBar(count($result));
                foreach ($result as $item) {
                    $this->info("current spider image url： $item->href");
                    $promise = $client->request('GET', $item->href);
                    sleep(1);
                    $promise->filter('.bqpp .bqppdiv1')->each(function ($node) use ($client) {
                        $fileInfo = getimagesize($node->filter('img')->attr('data-original'));
                        $content = [
                            'href' => $node->filter('img')->attr('data-original'),
                            'name' => $node->text(),
                            'width' => $fileInfo[0] || 100,
                            'height' => $fileInfo[1] || 100
                        ];
                        $this->data[] = $content;
                        $this->info(json_encode($content));
                    });
                    $this->info("successfully spider image url： $item->href");
                    $bar->advance();
                }
                $this->info("successfully spider dir：$filePath");
                // 1.打开文件 不存在则创建
                $fileName = public_path("json/$dir");
                if (!file_exists($fileName)) {
                    mkdir($fileName, 0777, true);
                }
                // 2.写入内容之前删除已有的文件
                $imageUrlPath = $fileName.DIRECTORY_SEPARATOR.'image-url.json';
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

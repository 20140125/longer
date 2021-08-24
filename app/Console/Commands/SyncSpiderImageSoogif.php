<?php

namespace App\Console\Commands;

use App\Models\Api\v1\SooGif;
use Exception;
use Goutte\Client;
use Illuminate\Console\Command;

class SyncSpiderImageSoogif extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'longer:sync-spider_image_form_soogif {url=https://bj.96weixin.com/material/soogif/761} {uuid=longer7f00000108fc00000001}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
    public function handle()
    {
        $this->getImageLists($this->argument('url'));
    }

    protected function getImageLists($url)
    {
        try {
            $client = new Client();
            $promise = $client->request('GET', $url);
            preg_match("/\d+/", $promise->filter('.float-page a')->first()->html(), $num);
            if ($promise->filter('.float-page a')->first()->html() && (int)$num[0] > 0) {
                $bar = $this->output->createProgressBar($num[0]);
                $arr = range(1, $num[0]);
                foreach ($arr as $id) {
                    $href = $url . '_' . $id;
                    $this->info('Current spider link：' . $href);
                    WebPush('Current spider link：' . $href, $this->argument('uuid'), 'command');
                    $promise = $client->request('GET', $href);
                    $promise->filter('.style-item')->each(function ($node) use ($client) {
                        $arr = ['href' => $node->attr('data-img'), 'name' => $node->filter('.item-tools h2')->text()];
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
            }
        } catch (Exception $exception) {
            $this->error($exception->getMessage());
        }
    }
}

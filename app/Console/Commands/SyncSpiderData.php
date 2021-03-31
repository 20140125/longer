<?php

namespace App\Console\Commands;

use Goutte\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class SyncSpiderData
 * @author <fl140125@gmail.com>
 * @package App\Console\Commands
 */
class SyncSpiderData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string $signature
     */
    protected $signature = 'longer:sync-spider-data';

    /**
     * The console command description.
     *
     * @var string $description
     */
    protected $description = 'sync spider data';
    /**
     * @var bool $flag
     */
    protected $flag;
    /**
     * @var int $startPage
     */
    protected $startPage;
    /**
     * @var int $startId
     */
    protected $startId;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->flag = true;
        $this->startPage = 1;
        $this->startId = 87003;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
//        $this->setFileInfo();
        $this->removeImage();
//        $this->getFaBiaoQing();
    }

    /**
     * todo:删除没有图片的关键词
     */
    protected function removeImage()
    {
//        $where[] = ['id', '<=', 104];
        $where[] = ['pid', '<>', 0];
        $result = DB::table('os_soogif_type')->orderByDesc('id')->where($where)->get();
        try {
            foreach ($result as $item) {
                $this->info(json_encode($item, JSON_UNESCAPED_UNICODE));
                $images = DB::table('os_soogif')->where('type', '=', $item->id)->get();
                if (count($images) === 0) {
                    $this->info($item->name.'没有图片');
                    DB::table('os_soogif_type')->where('id', '=', $item->id)->delete();
                }
            }
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }

    /**
     * todo:获取表情包
     */
    protected function getFaBiaoQingFromHeader()
    {
        global $currentId,$bar;
        try {
            $result = DB::table('os_soogif_type')->where('pid', '>', $this->startId)->orderByDesc('id')->get();
            $bar = $this->output->createProgressBar(count($result));
            $client=  new Client();
            foreach ($result as $item) {
                $this->startId = $item->pid;
                $currentId = $this->startId;
                $promise = $client->request('GET', $item->href);
                sleep(1);
                $this->info("抓取地址：".$item->href."\r\n");
                $promise->filter('.bqppdiv1 img')->each(function ($node) use ($client, $item) {
                    $href = str_replace('http://', 'https://', $node->attr('data-original'));
                    $this->info("抓取图片信息：".$node->attr('alt')."\r\n".$href."\r\n");
                    $result = DB::table('os_soogif')->where('href', '=', $href)->first(['href']);
                    !$result ? DB::table('os_soogif')->insert([
                        'type' => $item->id,
                        'href' => $href,
                        'name' => $node->attr('alt'),
                        'width' => 0,
                        'height' => 0
                    ]) : $this->line($href." :已经存在\r\n");
                });
            }
            sleep(1);
            $bar->advance();
        } catch (\Exception $exception) {
            $this->startId = $currentId;
            $this->getFaBiaoQingFromHeader();
            $this->error($exception);
        }
        $bar->finish();
    }
    /**
     * todo:获取表情包
     */
    protected function getFaBiaoQing()
    {
        global $currentId;
        $result = DB::table('os_soogif_type')->where('id', '>=', $this->startId)->orderBy('id')->get();
        try {
            $prefix = '/type/bq/page/';
            $client = new Client();
            foreach ($result as $item) {
                $this->startId = $item->id;
                $currentId = $this->startId;
                $arr = range(1, 20);
                foreach ($arr as $id) {
                    $this->info("当前抓取链接：".$item->href.$prefix.$id.'.html');
                    $promise = $client->request('GET', $item->href.$prefix.$id.'.html');
                    sleep(1);
                    $promise->filter('.searchbqppdiv')->each(function ($node) use ($client, $item) {
                        try {
                            $href = str_replace('http://', 'https://', $node->filter('a img')->attr('data-original'));
                            $this->info("抓取图片地址：".$href."\r\n");
                            $result = DB::table('os_soogif')->where('href', '=', $href)->first(['href']);
                            !$result? DB::table('os_soogif')->insert([
                                'type' => $item->id,
                                'href' => $href,
                                'name' => $item->name,
                                'width' => 0,
                                'height' => 0
                            ]) : $this->line($href." :已经存在\r\n");
                        } catch (\Exception $exception) {
                            $this->error($exception->getMessage());
                        }
                    });
                }
            }
            unset($result);
        } catch (\Exception $exception) {
            $this->startId = $currentId;
            $this->getFaBiaoQing();
            $this->error($exception->getMessage());
        }
    }
    /**
     * todo:获取表情类型
     */
    protected function getFaBiaoQingType()
    {
        global $currentPage;
        try {
            $client = new Client();
            $promise = $client->request('GET', 'https://fabiaoqing.com/search');
            $text = $promise->filter('#mobilepage')->text();
            $pages = explode('/', $text);
            $pageArr = range($this->startPage, (int)$pages[1]);
            $href = 'https://fabiaoqing.com/search/index/page/';
            foreach ($pageArr as $page) {
                $this->startPage = $page;
                $currentPage = $page;
                $this->info('请求的地址：'.$href.$this->startPage.".html\r\n");
                $promise = $client->request('GET', $href.$this->startPage.'.html');
                sleep(1);
                $promise->filter('#othersearch a')->each(function ($node) use ($client) {
                    $this->info("抓取的地址：".str_replace('http://', 'https://', $node->attr('href')));
                    $result = DB::table('os_soogif_type')
                        ->where('href', '=', str_replace('http://', 'https://', $node->attr('href')))
                        ->first();
                    !$result ? DB::table('os_soogif_type')->insert(
                        [
                            'href' => str_replace('http://', 'https://', $node->attr('href')),
                            'name' => $node->text(),
                            'pid' => 105
                        ]
                    ) :  $this->line("地址已经存在：".str_replace('http://', 'https://', $node->attr('href')));
                });
            }
        } catch (\Exception $exception) {
            $this->startPage = $currentPage;
            $this->getFaBiaoQingType();
            $this->error($exception->getMessage());
        }
    }

    /**
     * todo:获取文件信息
     */
    protected function setFileInfo()
    {
        global $result;
        try {
            while ($this->flag) {
                $result = DB::table('os_soogif')->where('width', '=', 0)->orderByDesc('id')->first();
                $this->flag = !empty($result);
                if (!empty($result)) {
                    $this->info("获取图片信息：\r\n".json_encode($result)."\r\n");
                    $fileInfo = getimagesize($result->href);
                    DB::table('os_soogif')->where('id', '=', $result->id)->update(
                        ['width' => $fileInfo[0], 'height' => $fileInfo[1]]
                    );
                    $this->info($result->href."\r\n图片信息修改成功\r\n".json_encode($fileInfo)."\r\n");
                }
            }
        } catch (\Exception $exception) {
            $this->error("失败原因：\r\n".$exception->getMessage()."\r\n");
            $this->error("失败图片：\r\n".json_encode($result)."\r\n");
            if ($result) {
                DB::table('os_soogif')->delete($result->id);
            }
            $this->setFileInfo();
        }
    }
    /**
     * todo:获取动态图
     */
    protected function getSooGif()
    {
        $result = DB::table('os_soogif_type')->where('pid', '>', 0)->get();
        $bar =  $bar = $this->output->createProgressBar(count($result));
        try {
            $client = new Client();
            foreach ($result as $item) {
                $promise = $client->request('GET', $item->href);
                //判断是否存在数据
                preg_match("/\d+/", $promise->filter('.float-page a')->first()->html(), $num);
                if ($promise->filter('.float-page a')->first()->html() && (int)$num[0] > 0) {
                    $this->info($promise->filter('.float-page a')->first()->html()."\r\n");
                    //获取分页参数
                    $arr = range(1, ceil((int)$num[0]/30));
                    foreach ($arr as $id) {
                        $href = $item->id<=84 ? mb_substr($item->href, 0, strrpos($item->href, '_')).'_'.$id
                            : $item->href.'&p='.$id;
                        $this->info($href."\r\n".$item->name."\r\n");
                        $promise = $client->request('GET', $href);
                        $promise->filter('.style-item')->each(function ($node) use ($client, $item) {
                            $this->info($node->attr('data-img')."\r\n".$node->filter('.item-tools h2')->text()."\r\n");
                            $result = DB::table('os_soogif')
                                ->where('href', '=', $node->attr('data-img'))
                                ->first(['href']);
                            $fileInfo = getimagesize($node->attr('data-img'));
                            !$result ? DB::table('os_soogif')->insert([
                                'type' => $item->id,
                                'href' => $node->attr('data-img'),
                                'name' => $node->filter('.item-tools h2')->text(),
                                'width' => $fileInfo[0],
                                'height' => $fileInfo[1]
                            ]) : $this->line($node->attr('data-img')." :已经存在\r\n");
                        });
                    }
                    sleep(0.5);
                    $bar->advance();
                } else {
                    $this->warn($item->name."：暂无数据\r\n");
                }
            }
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
        $bar->finish();
    }
    /**
     * todo:获取动态图类型
     */
    protected function getSooGifType()
    {
        $client = new Client();
        $promise = $client->request('GET', 'https://bj.96weixin.com/material/soogif');
        $promise->filter('.material-breadcrumb')->each(function ($node) use ($client) {
            $this->info($node->filter('cite')->text());
            $id = DB::table('os_soogif_type')->insertGetId([
                'name' => $node->filter('cite')->text(),
                'href' => config('app.url'),
                'pid' => 0
            ]);
            $node->filter('a')->each(function ($href) use ($client, $id) {
                $this->info($href->text()."\r\n".'https://bj.96weixin.com'.$href->attr('href'));
                DB::table('os_soogif_type')->insert([
                    'name' => $href->text(),
                    'href' => 'https://bj.96weixin.com'.$href->attr('href'),
                    'pid' => $id
                ]);
            });
        });
    }

    /**
     * todo:获取表情符号类型
     */
    protected function getEmoticonsType()
    {
        $client = new Client();
        $promise = $client->request('GET', 'https://bj.96weixin.com/tools/emoticons');
        $promise->filter('.tools-emoticons-category li a')->each(function ($node) use ($client) {
            if ($node->text() !== '全部') {
                DB::table('os_emoticons_type')->insert(
                    [
                        'id' => mb_substr($node->attr('href'), strrpos($node->attr('href'), '/')+1),
                        'name' => $node->text()
                    ]
                );
            }
        });
    }
    /**
     * todo:获取表情符号
     */
    protected function getEmoticons()
    {
        $client = new Client();
        $result = DB::table('os_emoticons_type')->get(['id','name']);
        foreach ($result as $item) {
            $promise = $client->request('GET', 'https://bj.96weixin.com/tools/emoticons/id/'.$item->id);
            $promise->filter('.tools-emoticons dd')->each(function ($node) use ($client, $item) {
                DB::table('os_emoticons')->insert(
                    [
                        'type' => $item->id,
                        'name' => $node->filter('p')->text(),
                        'icon' => $node->filter('textarea')->text()
                    ]
                );
            });
            $this->info('添加【'.$item->name.'】完成');
        }
    }
    /**
     * todo:获取特殊符号
     */
    protected function getSymbol()
    {
        $client = new Client();
        $promise = $client->request('GET', 'https://bj.96weixin.com/tools/symbol');
        $promise->filter('.tools-symbol dd p')->each(function ($node) use ($client) {
            DB::table('os_symbol')->insert(['data' => $node->text()]);
        });
    }
}

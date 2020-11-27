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
     * @var string
     */
    protected $signature = 'longer:sync-spider-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'sync spider data';

    protected $flag;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->flag = true;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->getRequestData();
    }

    protected function getRequestData()
    {
        $this->setFileInfo();
    }

    protected function setFileInfo()
    {
        global $result;
        try {
            while ($this->flag) {
                $result = DB::table('os_soogif')->where('width', '=', null)->orderByDesc('id')->first();
                $this->info("获取图片信息：\r\n".json_encode($result)."\r\n");
                $this->flag = !empty($result);
                $fileInfo = getimagesize($result->href);
                DB::table('os_soogif')->where('id', '=', $result->id)->update(
                    ['width' => $fileInfo[0], 'height' => $fileInfo[1]]
                );
                $this->info($result->href."\r\n图片信息修改成功\r\n".json_encode($fileInfo)."\r\n");
            }
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
            preg_match("/400 Bad Request/", $exception->getMessage(), $pregResult);
            if ($pregResult) {
                $this->error("删除访问失败的图片地址信息\r\n".json_encode($result)."\r\n");
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
            foreach ($result as $item) {
                $client = new Client();
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

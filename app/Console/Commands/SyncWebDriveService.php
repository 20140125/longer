<?php

namespace App\Console\Commands;

use App\Models\Api\v1\SooGif;
use Facebook\WebDriver\Chrome\ChromeDriver;
use Facebook\WebDriver\WebDriverBy;
use Illuminate\Console\Command;

class SyncWebDriveService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    /* https://www.fabiaoqing.com/tag/detail/id/?.html ? ID变量  */
    protected $signature = 'longer:sync-web_drive {url=https://www.fabiaoqing.com/biaoqing/lists/page/1.html} {source=1} {tab=false}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'web driver service';

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
        putenv('WEBDRIVER_CHROME_DRIVER=C://Users/v_llongfang/Desktop/Sign/chromedriver.exe');
        $this->argument('source') === 1 ? $this->getImageLists($this->argument('url')) : $this->getImageLists2($this->argument('url'));
    }

    /**
     * todo:数据抓取
     * @param $url  https://www.dbbqb.com/ (访问封IP, 需要充值会员)
     * @return void
     */
    protected function getImageLists2($url)
    {
        try {
            $driver = ChromeDriver::start();
            $driver->manage()->window()->maximize();
            $driver->get($url);
            foreach (range(1, 12, 2) as $k) {
                sleep(1);
                $js = "document.documentElement.scrollTop = document.documentElement.scrollHeight * {$k} / 10";
                $driver->executeScript($js);
            }
            $driver->executeScript($js);
            sleep(3);
            $this->spiderImage2($driver);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    protected function spiderImage2(ChromeDriver $driver)
    {
       try {
           $this->info($driver->getCurrentURL());
           // 按钮切换
           if ($this->argument('tab')) {
               $btn = $driver->findElements(WebDriverBy::cssSelector('.MuiTabs-flexContainer .MuiTab-wrapper'));
               foreach ($btn as $b) {
                   if ($b->getText() === '精选套图') {
                       $b->click();
                   }
               }
           }
           $elements = $driver->findElements(WebDriverBy::cssSelector('.jss49 .lazyload-wrapper'));
           foreach($elements as $item) {
               if ($this->hasElement($driver,'.jss51')) {
                   $arr = [
                       'href' => str_replace('//', 'https://', $item->findElement(WebDriverBy::cssSelector('.jss51'))->getAttribute('src')),
                       'name' => ['来呀，快活呀', '你怕不是逗比吧', '来啊，造作啊'][rand(0, 2)]
                   ];
                   if (SooGif::getInstance()->getOne(['href' => $arr['href']])) {
                       $this->warn('image already exists: ' . $arr['href']);
                   } else {
                       SooGif::getInstance()->saveOne(['href' => $arr['href'], 'name' => $arr['name']]);
                       $this->info('successfully save image： ' . $arr['href']);
                   }
               }
           }
       } catch (\Exception $e) {
           $driver->quit();
           sleep(5);
           $this->getImageLists2($this->argument('url'));
           $this->error($e->getMessage());
       }
    }

    /**
     * todo:判断节点是否存在
     * @param ChromeDriver $driver
     * @param string $element
     * @return bool
     */
    protected function hasElement(ChromeDriver $driver, string $element = '#tdialog #secur_code_change img'): bool
    {
        try {
            $driver->findElement(WebDriverBy::cssSelector($element));
            return true;
        } catch (\Exception $exception) {
            $this->info($exception->getMessage());
            return  false;
        }
    }

    /**
     * todo:获取图片
     * @param $url
     */
    protected function getImageLists($url)
    {
        try {
            $driver = ChromeDriver::start();
            $driver->manage()->window()->maximize();
            $driver->get($url);
            foreach (range(1, 9, 2) as $k) {
                sleep(1);
                $js = "document.documentElement.scrollTop = document.documentElement.scrollHeight * {$k} / 10";
                $driver->executeScript($js);
            }
            $this->spiderImage($driver);
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * todo:数据抓取
     * @param ChromeDriver $driver
     */
    protected function spiderImage(ChromeDriver $driver)
    {
        $this->info($driver->getCurrentURL());
        $source = $driver->findElements(WebDriverBy::cssSelector('.segment .tagbqppdiv'));
        foreach ($source as $item) {
            $arr = [
                'href' => $item->findElement(WebDriverBy::cssSelector('.image'))->getAttribute('src'),
                'name' => $item->findElement(WebDriverBy::cssSelector('.image'))->getAttribute('alt')
            ];
            if (SooGif::getInstance()->getOne(['href' => str_replace('http', 'https', $arr['href'])])) {
                $this->warn('image already exists: ' . str_replace('http', 'https', $arr['href']));
            } else {
                SooGif::getInstance()->saveOne(['href' => str_replace('http', 'https', $arr['href']), 'name' => mb_substr($arr['name'], 0, 50)]);
                $this->info('successfully save image： ' . str_replace('http', 'https', $arr['href']));
            }
        }
        sleep(5);
        /* todo:页码切换 */
        $this->currentPageChange($driver);
    }

    /**
     * todo:分页切换
     * @param ChromeDriver $driver
     * @return void
     */
    protected function currentPageChange(ChromeDriver $driver)
    {
        $pageButton = $driver->findElements(WebDriverBy::cssSelector('.pagination a'));
        foreach ($pageButton as $item) {
            if ($item->getText() === '下一页') {
                $item->click();
                sleep(5);
                $this->spiderImage($driver);
            }
        }
    }
}

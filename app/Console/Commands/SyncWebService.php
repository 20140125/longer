<?php

namespace App\Console\Commands;

use Facebook\WebDriver\Chrome\ChromeDriver;
use Facebook\WebDriver\Exception\NoSuchElementException;
use Facebook\WebDriver\Exception\WebDriverException;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverDimension;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SyncWebService extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'longer:sync-check-system {keywords?}';

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
     */
    public function handle()
    {
        putenv('WEBDRIVER_CHROME_DRIVER=D://python/chromedriver.exe');
        $this->startGoogleService();
    }
    /**
     * todo：数据抓取
     * @param string $url
     */
    protected function startGoogleService($url = 'https://www.jd.com')
    {
        $driver = ChromeDriver::start();
            //百度
//        $driver->get('https://www.baidu.com/');
//        $keywords = $driver->findElement(WebDriverBy::id('kw'));
//        $keywords->sendKeys('腾讯');
//        sleep(0.5);
//        $driver->findElement(WebDriverBy::id('su'))->click();
//        sleep(1);
//        $resource = $driver->findElements(WebDriverBy::cssSelector('#content_left h3 a'));
//        foreach ($resource as $item) {
//            $this->info($item->getAttribute('href'));
//            $this->info($item->getText());
//            $this->info("\r\n");
//        }
            //表情
//        $driver->get('https://fabiaoqing.com/search');
//        $resource = $driver->findElements(WebDriverBy::cssSelector('#othersearch a'));
//        foreach ($resource as $item) {
//            $this->info($item->getAttribute('href'));
//            $this->info($item->getText());
//            $this->info("\r\n");
//        }
            //Google
//        $driver->get('https://www.google.com/');
//        $keywords = $driver->findElement(WebDriverBy::name('q'));
//        $keywords->sendKeys('腾讯');
//        sleep(0.5);
//        $driver->getKeyboard()->pressKey(WebDriverKeys::ENTER);
//        $driver->getKeyboard()->releaseKey(WebDriverKeys::ENTER);
//        sleep(0.5);
//        $resource = $driver->findElements(WebDriverBy::cssSelector('#rso a'));
//        foreach ($resource as $item) {
//            $this->info($item->getAttribute('href'));
//            $this->info($item->getText());
//            $this->info("\r\n");
//        }
        //京东商品
        $size = new WebDriverDimension(1920, 1048);
        $driver->manage()->window()->setSize($size);
        $driver->get($url);
        $keywords = $driver->findElement(WebDriverBy::id('key'));
        $keywords->sendKeys($this->argument('keywords') ?? '飞天茅台');
        sleep(2);
        $driver->findElement(WebDriverBy::className('button'))->click();
        sleep(3);
        $this->getRequestJDItems($driver);
    }

    /**
     * todo:获取数据
     * @param ChromeDriver $driver
     */
    protected function getRequestJDItems(ChromeDriver $driver)
    {
        foreach (range(1, 9, 2) as $k) {
            sleep(2);
            $js = "document.documentElement.scrollTop = document.documentElement.scrollHeight * {$k} / 10";
            $this->info($js);
            $driver->executeScript($js);
        }
        $resource = $driver->findElements(WebDriverBy::className('gl-item'));
        $arr = [];
        foreach ($resource as $item) {
            # 商品链接
            $json['item_url'] = $this->hasElement($driver, WebDriverBy::cssSelector('.p-img a')) ?
                $item->findElement(WebDriverBy::cssSelector('.p-img a'))->getAttribute('href') : '';
            # 商品图片
            $json['src'] = $this->hasElement($driver, WebDriverBy::cssSelector('.p-img img')) ?
                $item->findElement(WebDriverBy::cssSelector('.p-img img'))->getAttribute('src') : '';
            # 商品价格
            $json['price'] = $this->hasElement($driver, WebDriverBy::cssSelector('.p-price i')) ?
                $item->findElement(WebDriverBy::cssSelector('.p-price i'))->getText() : '';
            # 商品名称
            $json['name'] = $this->hasElement($driver, WebDriverBy::cssSelector('.p-name em')) ?
                $item->findElement(WebDriverBy::cssSelector('.p-name em'))->getText() : '';
            # 商品评价
            $json['commit'] = $this->hasElement($driver, WebDriverBy::cssSelector('.p-commit strong')) ?
                $item->findElement(WebDriverBy::cssSelector('.p-commit strong'))->getText() : '';
            # 店铺名称
            $json['shop_name'] = $this->hasElement($driver, WebDriverBy::cssSelector('.p-shop')) ?
                $item->findElement(WebDriverBy::cssSelector('.p-shop'))->getText() : '';
            # 店铺地址
            $json['shop_url'] = '';
            if ($json['shop_name']) {
                $json['shop_url'] = $this->hasElement($driver, WebDriverBy::cssSelector('.p-shop a')) ?
                    $item->findElement(WebDriverBy::cssSelector('.p-shop a'))->getAttribute('href') : '';
            }
            # 輸出信息
            $this->info(json_encode($json, JSON_UNESCAPED_UNICODE));
            $this->info("\r\n");
            $arr[] = $json;
        }
        # 獲取當前頁
        $currentPage = $driver->findElement(WebDriverBy::cssSelector('#J_bottomPage .curr'))->getText();
        # 獲取總頁數
        $totalPage = $driver->findElement(WebDriverBy::cssSelector('#J_bottomPage .p-skip b'))->getText();
        if (intval($currentPage) < intval($totalPage)) {
            Log::error(json_encode($arr, JSON_UNESCAPED_UNICODE));
            $driver->findElement(WebDriverBy::className('pn-next'))->click();
            $this->info($driver->getCurrentURL());
            # 休息30S
            sleep(30);
            $this->info('休眠30秒');
            $this->getRequestJDItems($driver);
        } elseif (intval($currentPage) === intval($totalPage)) {
            Log::error(json_encode($arr, JSON_UNESCAPED_UNICODE));
            $driver->close();
        }
    }
    /**
     * 判断元素是否存在
     * @param ChromeDriver $driver
     * @param WebDriverBy $locator
     * @return bool
     */
    protected function hasElement(ChromeDriver $driver, WebDriverBy $locator): bool
    {
        try {
            $driver->findElement($locator);
            return true;
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            return false;
        }
    }
}

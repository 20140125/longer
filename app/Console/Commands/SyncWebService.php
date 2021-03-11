<?php

namespace App\Console\Commands;

use Facebook\WebDriver\Chrome\ChromeDriver;
use Facebook\WebDriver\WebDriverBy;
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
//        system('start D://python/jd_seckill/qr_code.png');
//        return;
        $this->startGoogleService();
    }

    protected function getVerifyCode()
    {
        $driver = ChromeDriver::start();
        $driver->manage()->window()->maximize();   //全屏
        $driver->get('https://www.fanglonger.com/login');
        $driver->takeScreenshot('login.png');
        sleep(4);
        $verifyCode = $driver->findElement(WebDriverBy::cssSelector('#s-canvas'));
        $location = $verifyCode->getLocationOnScreenOnceScrolledIntoView();
        $imageResource = getimagesize('login.png');
//        $right = $location['x'] + $imageResource[0];
//        $bottom = $location['y'] + $imageResource[1];
        $this->info(json_encode([$location,$imageResource]));
    }
    /**
     * todo：自动签入签出
     */
    protected function startWebService()
    {
        /* 程序启动 */
        $driver = ChromeDriver::start();
        $driver->manage()->window()->maximize();   //全屏
        $driver->get('http://om.tencent.com/attendances/check_out');
        /* 获取文本框 */
        $username = $driver->findElement(WebDriverBy::id('username'));
        $username->sendKeys('v_llongfang');
        sleep(2);
        $password = $driver->findElement(WebDriverBy::id('password_input'));
        $password->sendKeys('xb8062XBR');
        sleep(2);
        /* 点击按钮 */
        $driver->findElement(WebDriverBy::id('rememberButton'))->click();
        sleep(2);
        $driver->findElement(WebDriverBy::id('login_button'))->click();
        sleep(5);
        if (time() >= strtotime(date('Y-m-d 19:i:s'))) {
            $driver->findElement(WebDriverBy::id('checkout_btn'))->click();
        } else {
            $driver->findElement(WebDriverBy::id('checkin_btn'))->click();
        }
        sleep(5);
        $driver->findElement(WebDriverBy::cssSelector('#tdialog-buttonwrap .btn-primary'))->click();
        sleep(5);
        $driver->close();
    }
    /**
     * todo：数据抓取
     * @param string $url
     */
    protected function startGoogleService($url = 'https://www.jd.com')
    {
        $driver = ChromeDriver::start();
        //京东商品
        $driver->manage()->window()->maximize();   //全屏
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

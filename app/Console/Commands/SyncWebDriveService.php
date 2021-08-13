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
    protected $signature = 'longer:sync-web_drive {url=https://www.fabiaoqing.com/biaoqing/lists/page/1.html}';

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
    public function handle()
    {
        putenv('WEBDRIVER_CHROME_DRIVER=D://python/chromedriver.exe');
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
                $this->warn('image already exists: '. str_replace('http', 'https', $arr['href']));
            } else {
                SooGif::getInstance()->saveOne(['href' => str_replace('http', 'https', $arr['href']), 'name' => mb_substr($arr['name'], 0, 50)]);
                $this->info('successfully save image： '. str_replace('http', 'https', $arr['href']));
            }
        }
        sleep(5);
        /* todo:页码切换 */
        $this->currentPageChange($driver);
    }
    /* todo:分页切换 */
    protected function currentPageChange(ChromeDriver $driver)
    {
        $pageButton = $driver->findElements(WebDriverBy::cssSelector('.pagination a'));
        foreach($pageButton as $item) {
            if ($item->getText() === '下一页') {
                $item->click();
                sleep(5);
                $this->spiderImage($driver);
            }
        }
    }
}

<?php

namespace App\Console\Commands;

use App\Http\Controllers\Utils\Rsa;
use Curl\Curl;
use Facebook\WebDriver\Chrome\ChromeDriver;
use Facebook\WebDriver\WebDriverBy;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'longer:login-system-sign_in {username=v_llongfang} {password=xb8062XBR}';

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
        date_default_timezone_set('Asia/Shanghai');
        putenv('WEBDRIVER_CHROME_DRIVER=D://python/chromedriver.exe');
    }

    /**
     * Execute the console command.
     * @return void
     */
    public function handle()
    {

    }

}

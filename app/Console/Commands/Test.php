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
            global $driver;
            try {
                $driver = ChromeDriver::start();
                $driver->manage()->window()->maximize();
                $driver->get('http://om.tencent.com/attendances/check_out');
                Log::info(date('Y-m-d H:i:s', time()). '系统准备签入签出，打开浏览器窗口');
                sleep(2);
                $driver->findElement(WebDriverBy::id('username'))->sendKeys($this->argument('username'));
                $this->info('用户名：'.$this->argument('username'));
                sleep(2);
                $driver->findElement(WebDriverBy::id('password_input'))->sendKeys($this->argument('password'));
                $this->info('用户密码：'.$this->argument('password'));
                sleep(2);
                $driver->findElement(WebDriverBy::id('rememberButton'))->click();
                sleep(1);
                $driver->findElement(WebDriverBy::id('login_button'))->click();
                sleep(5);
                $this->info("恭喜{$this->argument('username')}成功登录系统");
                if (date('H', time()) >= 19) {
                    $this->info('19点后可以签出系统');
                    $driver->findElement(WebDriverBy::id('checkout_btn'))->click();
                } else if (date('H', time()) <= 9) {
                    $this->info('9点之前签入系统');
                    $driver->findElement(WebDriverBy::id('checkin_btn'))->click();
                } else {
                    $this->info('不在签入签出范围内');
                }
                sleep(5);
                $this->info('准备确认系统签入签出');
                $driver->findElement(WebDriverBy::cssSelector("#tdialog-buttonwrap .btn-primary"))->click();
                sleep(5);
                $this->info('系统签入签出成功，关闭浏览器');
                Log::info(date('Y-m-d H:i:s', time()). '系统签入签出成功，关闭浏览器');
                $driver->quit();
            } catch (\Exception $exception) {
                $driver->quit();
                $this->info($exception->getMessage());
                Log::error($exception->getMessage());
            }
        }

    }

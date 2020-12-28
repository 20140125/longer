<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        'App\Console\Commands\SyncOauth',
        'App\Console\Commands\SyncExpiresRule',
        'App\Console\Commands\SyncNormalRule',
        'App\Console\Commands\SyncSendPush',
        'App\Console\Commands\SyncAreaLists',
        'App\Console\Commands\SyncOauthToUsers',
        'App\Console\Commands\SyncLocal',
        'App\Console\Commands\SyncWeather',
        'App\Console\Commands\SyncClientId',
        'App\Console\Commands\SyncChatMessage',
        'App\Console\Commands\SyncSpiderData',
        'App\Console\Commands\SyncWebService'
    ];

    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}

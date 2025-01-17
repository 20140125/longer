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
        'Test'                   => 'App\Console\Commands\Test',
        'SyncOauth'              => 'App\Console\Commands\SyncOauth',
        'SyncWebPush'            => 'App\Console\Commands\SyncWebPush',
        'SyncCityWeather'        => 'App\Console\Commands\SyncCityWeather',
        'SyncSpiderImage'        => 'App\Console\Commands\SyncSpiderImage',
        'SyncSpiderImageType'    => 'App\Console\Commands\SyncSpiderImageType',
        'SyncWebDriveService'    => 'App\Console\Commands\SyncWebDriveService',

    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}

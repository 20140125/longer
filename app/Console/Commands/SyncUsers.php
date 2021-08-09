<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'longer:sync-users { remember_token=default }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'synchronizing Users information from userCenter';

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
     * @return int
     */
    public function handle()
    {
        $this->synchronizedUser();
    }

    protected function synchronizedUser()
    {
        $this->info('synchronized user information');
    }
}

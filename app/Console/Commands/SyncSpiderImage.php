<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SyncSpiderImage extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'longer:sync-spider_image';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'synchronize image';

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
        return 0;
    }
}

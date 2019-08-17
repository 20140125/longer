<?php

namespace App\Console\Commands\worker;

use Illuminate\Console\Command;

class AuthServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'WorkerMan:auth {action=start : start | restart | reload | stop | status | connections} {--d : daemon or debug}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'auth server';

    /**
     * Create a new command instance.
     * AuthServer constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     *
     */
    public function handle()
    {
        global $argv;
        $argv[0] = 'WorkerMan:auth';
        $argv[1] = $this->argument('action');
        $argv[2] = $this->option('d') ? '-d' : ''; // 守护进程模式或调试模式启动
    }
}

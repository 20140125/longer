<?php

namespace App\Console\Commands\worker;

use Illuminate\Console\Command;
use GatewayWorker\BusinessWorker;
use GatewayWorker\Gateway;
use GatewayWorker\Register;
use Workerman\Worker;

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
        $this->start();
    }
    private function start()
    {
        $this->startGateWay();
        $this->startBusinessWorker();
        $this->startRegister();
        Worker::runAll();
    }

    private function startBusinessWorker()
    {
        $worker                  = new BusinessWorker();
        $worker->name            = 'BusinessWorker';
        $worker->count           = 1;
        $worker->registerAddress = '127.0.0.1:1236';
        $worker->eventHandler    = \App\Events\WorkerMan\Events::class;
    }

    private function startGateWay()
    {
        $gateway = new Gateway("websocket://127.0.0.1:2346");
        $gateway->name                 = 'Gateway';
        $gateway->count                = 1;
        $gateway->lanIp                = '127.0.0.1';
        $gateway->startPort            = 2300;
        $gateway->pingInterval         = 30;
        $gateway->pingNotResponseLimit = 0;
        $gateway->pingData             = '{"type":"@heart@"}';
        $gateway->registerAddress      = '127.0.0.1:1236';
    }

    private function startRegister()
    {
        new Register('text://0.0.0.0:1236');
    }
}

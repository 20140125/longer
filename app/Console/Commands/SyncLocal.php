<?php

namespace App\Console\Commands;

use App\Http\Controllers\Utils\Amap;
use App\Models\Log;
use Illuminate\Console\Command;

class SyncLocal extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'longer:sync_local';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'sync local from ip address';
    /**
     * @var Log $logModel
     */
    protected $logModel;
    /**
     * @var Amap $amapUtils
     */
    protected $amapUtils;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->logModel = Log::getInstance();
        $this->amapUtils = Amap::getInstance();
    }

    /**
     * todo:获取日志列表
     */
    protected function getLogLists()
    {
        $result = $this->logModel->getLists(0,0,date('Ymd'));
        foreach ($result as $item) {
            $localObj =  json_decode($this->amapUtils->getAddress($item->ip_address),true);
            $item->local = $localObj['province'].','.$localObj['city'];
            $this->logModel->addResult(object_to_array($item));
        }
    }
    /**
     * Execute the console command.
     **/
    public function handle()
    {
        $this->getLogLists();
    }
}

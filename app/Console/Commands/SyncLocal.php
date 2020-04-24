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
    protected function getLogLists($date)
    {
        foreach ($date as $row) {
            $result = $this->logModel->getLists(0,0,date('Ymd',$row));
            foreach ($result as $item) {
                $localObj =  object_to_array($this->amapUtils->getAddress($item->ip_address));
                $item->local = $localObj['province'].','.$localObj['city'];
                $this->logModel->updateResult(['local'=>$item->local],['id'=>$item->id]);
            }
        }
    }
    /**
     * Execute the console command.
     **/
    public function handle()
    {
        $date = range(strtotime('20191203'),strtotime(date('Ymd')),24*60*60);
        $this->getLogLists($date);
    }
}

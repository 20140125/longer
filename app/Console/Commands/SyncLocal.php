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
     * @var string $signature
     */
    protected $signature = 'longer:sync-local';

    /**
     * The console command description.
     *
     * @var string $description
     */
    protected $description = 'sync local from amap to ip address';
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
        $result = $this->logModel->getLists(0, 0, date('Ymd'));
        $bar = $this->output->createProgressBar(count($result));
        foreach ($result as $item) {
            if (empty($item->local)) {
                $localObj =  objectToArray($this->amapUtils->getAddress($item->ip_address));
                $province = $localObj['province'] ??  '亚洲';
                $city =  $localObj['city'] ?? '中国';
                $this->logModel->updateResult(['local'=>$province.", ".$city], ['id'=>$item->id]);
                $this->info(json_encode($localObj, JSON_UNESCAPED_UNICODE));
            }
            sleep(0.5);
            $bar->advance();
        }
        $this->info("system local sync success\r\n");
        $bar->finish();
    }
    /**
     * Execute the console command.
     **/
    public function handle()
    {
        $this->getLogLists();
    }
}

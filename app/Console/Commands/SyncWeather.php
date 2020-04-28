<?php

namespace App\Console\Commands;

use App\Http\Controllers\Utils\Amap;
use App\Models\Area;
use Illuminate\Console\Command;

class SyncWeather extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'longer:sync_weather';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'sync weather from amap to area table';
    /**
     * @var Amap $amapUtils
     */
    protected $amapUtils;
    /**
     * @var Area $areaModel
     */
    protected $areaModel;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->amapUtils = Amap::getInstance();
        $this->areaModel = Area::getInstance();
    }

    /**
     * TODO:同步城市天气
     */
    protected function getAreaLists()
    {
        $bar = $this->output->createProgressBar(count($this->areaModel->getAll(['id'])));
        $groupResult = $this->areaModel->getListsGroupByParentId();
        foreach ($groupResult as $row) {
            $this->info("当前同步省份是：".$this->areaModel->getResult('id',$row->id,'=',['name'])->name);
            $result = $this->areaModel->getResultLists($row->id);
            foreach ($result as $item) {
                $weatherObj = object_to_array($this->amapUtils->getWeather($item->code));
                $item->info = $weatherObj['info'] == 'OK' ? json_encode($weatherObj['lives'][0],JSON_UNESCAPED_UNICODE) : '';
                $this->areaModel->updateResult(object_to_array($item),'id',$item->id);
                $bar->advance();
                $this->info("当前同步城市是：".$this->areaModel->getResult('id',$item->id,'=',['name'])->name);
                sleep(1);
            }
        }
        $bar->advance();
        $bar->finish();

    }
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->getAreaLists();
    }
}

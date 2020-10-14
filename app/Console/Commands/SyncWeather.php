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
        try {
            foreach ($groupResult as  $row) {
                $this->info("当前同步的是：".$this->areaModel->getResult('id',$row->id,'=',['name'])->name);
                $provinceWeather = object_to_array($this->amapUtils->getWeather($row->code,'all'));
                if (!empty($provinceWeather)) {
                    $provinceForecast = $provinceWeather['info'] == 'OK' ? $provinceWeather['forecasts'][0] : '';
                    $provinceInfo = [
                        'city' => !empty($provinceForecast['city']) ? $provinceForecast['city'] : '',
                        'adcode' => !empty($provinceForecast['adcode']) ? $provinceForecast['adcode'] : '',
                        'province' => !empty($provinceForecast['province']) ? $provinceForecast['province'] : '',
                        'reporttime' => !empty($provinceForecast['reporttime']) ? $provinceForecast['reporttime'] : '',
                        'casts' => !empty($provinceForecast['casts']) ? $provinceForecast['casts'][0] : ''
                    ];
                    $this->areaModel->updateResult(['info' => json_encode($provinceInfo, JSON_UNESCAPED_UNICODE), 'forecast' => json_encode($provinceForecast, JSON_UNESCAPED_UNICODE)], 'id', $row->id);
                    $this->info($this->areaModel->getResult('id', $row->id, '=', ['name'])->name . "：同步成功【{$row->id}】");
                }
            }
            $bar->advance();
            $bar->finish();
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->getAreaLists();
    }
}

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
        $cityMap = range(2,35);
        foreach ($groupResult as $row) {
            $this->info("当前同步省份是：".$this->areaModel->getResult('id',$row->id,'=',['name'])->name);
            $provinceWeather = object_to_array($this->amapUtils->getWeather($row->code,'all'));
            if (!empty($provinceWeather)){
                $provinceForecast = $provinceWeather['info'] == 'OK' ? $provinceWeather['forecasts'][0] : '';
                $provinceInfo =  [
                    'city' => $provinceForecast['city'],
                    'adcode' => $provinceForecast['adcode'],
                    'province' => $provinceForecast['province'],
                    'reporttime' => $provinceForecast['reporttime'],
                    'casts' => $provinceForecast['casts'] ? $provinceForecast['casts'][0] : ''
                ];
                $this->areaModel->updateResult(['info'=>json_encode($provinceInfo,JSON_UNESCAPED_UNICODE),'forecast'=>json_encode($provinceForecast,JSON_UNESCAPED_UNICODE)],'id',$row->id);
                $result = $this->areaModel->getResultLists($row->id);
                foreach ($result as $item) {
                    if (in_array($item->parent_id,$cityMap)) {
                        $cityWeather = object_to_array($this->amapUtils->getWeather($item->code,'all'));
                        if (!empty($cityWeather)) {
                            $cityForecast = $cityWeather['info'] == 'OK' ? $cityWeather['forecasts'][0] : '';
                            $cityInfo =  [
                                'city' => $cityForecast['city'],
                                'adcode' => $cityForecast['adcode'],
                                'province' => $cityForecast['province'],
                                'reporttime' => $cityForecast['reporttime'],
                                'casts' => $cityForecast['casts'] ? $cityForecast['casts'][0] : ''
                            ];
                            $this->areaModel->updateResult(['info'=>json_encode($cityInfo,JSON_UNESCAPED_UNICODE),'forecast'=>json_encode($cityForecast,JSON_UNESCAPED_UNICODE)],'id',$item->id);
                            $bar->advance();
                            $this->info("当前同步城市是：".$this->areaModel->getResult('id',$item->id,'=',['name'])->name);
                            sleep(0.5);
                        }
                    } else {
                        $cityWeather = object_to_array($this->amapUtils->getWeather($item->code));
                        if (!empty($cityWeather)) {
                            $cityInfo = $cityWeather['info'] == 'OK' ? json_encode($cityWeather['lives'][0],JSON_UNESCAPED_UNICODE) : '';
                            $this->areaModel->updateResult(['info'=>$cityInfo,'forecast'=>$cityInfo],'id',$item->id);
                            $bar->advance();
                            $this->info("当前同步城市是：".$this->areaModel->getResult('id',$item->id,'=',['name'])->name);
                            sleep(0.5);
                        }
                    }
                }
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

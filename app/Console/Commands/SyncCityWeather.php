<?php

namespace App\Console\Commands;

use App\Http\Controllers\Service\v1\AreaService;
use App\Http\Controllers\Utils\AMap;
use App\Models\Api\v1\Area;
use Illuminate\Console\Command;

class SyncCityWeather extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'longer:sync-city_weather';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync city weather from Gothe API';

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
     * @return void
     */
    public function handle()
    {
        $this->syncCityWeather();
        sleep(1);
        $this->info('Start synchronized city weather');
        AreaService::getInstance()->getCacheArea(['type' => 'weather']);
        $this->info('Finished synchronized city weather');
    }

    /**
     * todo:同步城市天气
     */
    protected function syncCityWeather()
    {
        try {
            $lists = Area::getInstance()->getAreaLists([['id' ,'>' , 1]]);
            $bar = $this->output->createProgressBar(count($lists));
            $form = [];
            foreach ($lists as $item) {
                $this->info('currently synchronized city weather is：【'.$item->name.'】');
                $_weather = (array)AMap::getInstance()->getWeather($item->code ,'all');
                if (!empty($_weather['code'])) {
                    $this->error($_weather['message']);
                    continue;
                }
                $form['forecast'] = !empty($_weather['info']) ? ($_weather['info'] == 'OK' ? $_weather['forecasts'][0] : '') : '';
                $form['info'] = array(
                    'city' => !empty($form['forecast']['city']) ? $form['forecast']['city'] : '',
                    'adcode' => !empty($form['forecast']['adcode']) ? $form['forecast']['adcode'] : '',
                    'province' => !empty($form['forecast']['province']) ? $form['forecast']['province'] : '',
                    'reporttime' => !empty($form['forecast']['reporttime']) ? $form['forecast']['reporttime'] : '',
                    'casts' => !empty($form['forecast']['casts']) ? $form['forecast']['casts'][0] : ''
                );
                $json = ['info', 'forecast'];
                $form['updated_at'] = date('Y-m-d H:i:s');
                foreach ($json as $str) {
                    $form[$str] = json_encode($form[$str], JSON_UNESCAPED_UNICODE);
                }
                if (Area::getInstance()->updateOne(['id' => $item->id], $form)) {
                    $this->info('Successfully synchronized city weather is：【'.$item->name.'】');
                } else {
                    $this->info('Failed synchronized city weather is：【'.$item->name.'】');
                }
                usleep(rand(500000, 700000));
                $bar->advance();
            }
            $this->info('Successfully synchronized city weather');
            $bar->finish();
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
        }
    }
}

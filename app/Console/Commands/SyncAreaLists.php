<?php

namespace App\Console\Commands;

use App\Http\Controllers\Utils\RedisClient;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * Class Chat
 * @author <fl140125@gmail.com>
 * @package App\Console\Commands
 */
class SyncAreaLists extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'longer:sync_area';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync area lists';
    /**
     * @var RedisClient
     */
    protected $redisClient;

    protected $areaModel;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->redisClient = new RedisClient();
        $this->areaModel = \App\Models\Area::getInstance();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->syncCityWeather();
    }

    /**
     * TODO:同步城市天气
     */
    protected function syncCityWeather()
    {
        $result = Cache::get('city_weather');
        if (empty($result)) {
            $result = get_tree($this->areaModel->getAll(['code','info','parent_id','id','name']),1,'children','parent_id');
            Cache::put('city_weather',json_encode($result,JSON_UNESCAPED_UNICODE),Carbon::now()->addMinutes(120));
            $this->info('城市列表已经同步到Cache');
        }
    }
}

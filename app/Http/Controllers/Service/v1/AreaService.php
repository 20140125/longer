<?php

namespace App\Http\Controllers\Service\v1;

use App\Http\Controllers\Utils\AMap;
use App\Http\Controllers\Utils\Code;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Cache;

class AreaService extends BaseService
{
    /**
     * @var static $instance
     */
    private static $instance;
    /**
     * @return static
     */
    public static function getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * todo:获取城市列表
     * @param $form
     * @param bool $getAll
     * @return array
     */
    public function getAreaLists($form, bool $getAll = false, $columns = ['id','parent_id as pid', 'name', 'code', 'info', 'forecast'])
    {
        if ($getAll) {
            $result = Cache::get('__cache_area');
            if(!empty($result)) {
                $this->return['lists'] = json_decode($result, true);
                return $this->return;
            }
            $this->return['lists'] = $this->areaModel->getAreaLists(['parent_id' => $form['parent_id']], $columns);
            Cache::forever('__cache_area', json_encode($this->return['lists'], JSON_UNESCAPED_UNICODE));
            return $this->return;
        }
        $this->return['lists'] = $this->areaModel->getAreaLists(['parent_id' => $form['parent_id']], $columns);
        foreach ($this->return['lists'] as &$item) {
            $item->hasChildren = false;
            $item->info = json_decode($item->info, true);
            $item->forecast = json_decode($item->forecast, true);
            if ($this->areaModel->getOne(['parent_id' => $item->id])) {
                $item->hasChildren = true;
            }
        }
        return $this->return;
    }

    /**
     * todo：获取天气预报
     * @param $form
     * @return array
     */
    public function getAreaWeather($form)
    {
        try {
            $_weather = (array)AMap::getInstance()->getWeather($form['code'] ,'all');
            if (!empty($_weather['code'])) {
                $this->return = $_weather;
                return $this->return;
            }
            $form['forecast'] = !empty($_weather['info']) ? ($_weather['info'] == 'OK' ? $_weather['forecasts'][0] : '') : '';
            $form['info'] = $form['forecast'];
            $form['info']['casts'] = ($form['info']['casts'] ?? [])[0] || '';
            $json = ['info', 'forecast'];
            $form['updated_at'] = date('Y-m-d H:i:s');
            foreach ($json as $item) {
                $form[$item] = json_encode($form[$item], JSON_UNESCAPED_UNICODE);
            }
            $this->areaModel->updateOne(['code' => $form['code']], $form);
            $this->redisClient->setValue($form['code'], ['info' => $form['info'], 'forecast' => $form['forecast']], ['EX' => 3600]);
            $this->return['lists'] = $form;
            return $this->return;
        } catch (\Exception $exception) {
            return ['code' => Code::ERROR, 'message' => $exception->getMessage()];
        }
    }

    public function updateArea($form)
    {

    }
    /**
     * todo:获取城市缓存
     * @param array $form
     * @param bool $getAll
     * @return array
     */
    public function getCacheArea(array $form = [], bool $getAll = true)
    {
        $attr = !empty($form['type']) ? ['__cache_val' => '_weather', 'fields' => ['code', 'info', 'parent_id', 'id', 'name'], 'timeout' => 1] : ['__cache_val' => '_center', 'fields' => ['parent_id', 'id', 'name'], 'timeout' => 0];
        $result = Cache::get($attr['__cache_val']);
        if(!empty($result)) {
            $this->return['lists'] = json_decode($result, true);
            return $this->return;
        }
        $where = [['id' ,'>' , 0]];
        $result = $this->areaModel->getAreaLists($where, $attr['fields']);
        $attr['timeout'] ? Cache::put($attr['__cache_val'],json_encode($result, 256), Carbon::now()->addHours(2)) : Cache::forever($attr['__cache_val'], json_encode($result, JSON_UNESCAPED_UNICODE));
        $this->return['lists'] = $result;
        return $this->return;
    }
    /**
     * todo:获取用户
     * @param $where
     * @param string[] $columns
     * @return Model|Builder|object|null
     */
    public function getArea($where, array $columns = ['*'])
    {
        return $this->areaModel->getOne($where, $columns);
    }
}

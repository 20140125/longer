<?php

namespace App\Http\Controllers\Service\v1;

use App\Http\Controllers\Utils\Code;
use App\Jobs\SyncImageListsProcess;
use App\Jobs\SyncImageTypeProcess;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class ToolsService extends BaseService
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
     * todo:获取定位
     * @param $form
     * @return array|int|mixed|null
     */
    public function getAddress($form)
    {
        $result = (array)$this->aMapUtils->getAddress($form['ip_address']);
        if (!empty($result['code'])) {
            return $result;
        }
        if(gettype($result) === 'boolean') {
            $this->return['code'] = Code::ERROR;
            $this->return['message'] = 'Failed get Address';
            return $this->return;
        }
        $this->return['lists'] = $result;
        return $this->return;
    }

    /**
     * todo:获取天气
     * @param $form
     * @return array
     */
    public function getWeather($form)
    {
        $result = $this->areaModel->getOne(['name' => $form['city_name']], ['forecast']);
        if (!$result) {
            $this->return['code'] = Code::ERROR;
            $this->return['message'] = 'Failed get weather info';
            return $this->return;
        }
        $this->return['lists'] = $result;
        return $this->return;
    }
    /**
     * todo:同步图片类型
     * @param $form
     * @return array
     */
    public function syncImageType($form)
    {
        dispatch(new SyncImageTypeProcess($form))->onQueue('syncImageType');
        return $this->return;
    }
    /**
     * todo:同步图片
     * @param $form
     * @return array
     */
    public function syncImageLists($form)
    {
        dispatch(new SyncImageListsProcess($form))->onQueue('syncImageLists');
        return $this->return;
    }
}

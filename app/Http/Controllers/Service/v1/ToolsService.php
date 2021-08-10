<?php

namespace App\Http\Controllers\Service\v1;

use App\Http\Controllers\Utils\Code;
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
}

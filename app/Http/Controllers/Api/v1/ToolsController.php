<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ToolsController extends BaseController
{
    /**
     * 获取地址
     * @return JsonResponse
     */
    public function getAddress()
    {
        validatePost($this->post, ['ip_address' => 'required|string|ip']);
        $result = $this->toolService->getAddress($this->post);
        return ajaxReturn($result);
    }

    /**
     * 获取城市天气
     * @return JsonResponse
     */
    public function getWeather()
    {
        validatePost($this->post, ['city_name' => 'required|string']);
        $result = $this->toolService->getWeather($this->post);
        return ajaxReturn($result);
    }
}

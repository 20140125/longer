<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\JsonResponse;

class ToolsController extends BaseController
{
    /**
     * todo:获取地址
     * @return JsonResponse
     */
    public function getAddress()
    {
        validatePost($this->post, ['ip_address' => 'required|string|ip']);
        $result = $this->toolService->getAddress($this->post);
        return ajaxReturn($result);
    }

    /**
     * todo:获取城市天气
     * @return JsonResponse
     */
    public function getWeather()
    {
        validatePost($this->post, ['city_name' => 'required|string']);
        $result = $this->toolService->getWeather($this->post);
        return ajaxReturn($result);
    }

    /**
     * todo:获取图片列表
     * @return JsonResponse
     */
    public function syncImageType()
    {
        validatePost($this->post, ['keywords' => 'required|string']);
        $result = $this->toolService->syncImageType($this->post);
        return ajaxReturn($result);
    }
}

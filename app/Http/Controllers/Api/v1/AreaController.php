<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AreaController extends BaseController
{
    /**
     * todo:获取成功列表
     * @return JsonResponse
     */
    public function getAreaLists()
    {
        validatePost($this->post, ['parent_id' => 'required|integer']);
        $result = $this->areaService->getAreaLists($this->post);
        return ajaxReturn($result);
    }

    /**
     * todo:获取城市天气
     * @return JsonResponse
     */
    public function getAreaWeather()
    {
        validatePost($this->post, ['code' => 'required|string|exists:os_china_area']);
        $result = $this->areaService->getAreaWeather($this->post);
        return ajaxReturn($result);
    }

    /**
     * todo:获取缓存数据
     * @return JsonResponse
     */
    public function getCacheArea()
    {
        $result = $this->areaService->getCacheArea();
        return ajaxReturn($result);
    }
}

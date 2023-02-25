<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ToolsController extends BaseController
{
    /**
     * 获取地址
     * @param Request $request
     * @return JsonResponse
     */
    public function getAddress(Request $request): JsonResponse
    {
        validatePost($request->get('item'), $this->post, ['ip_address' => 'required|string|ip']);
        $result = $this->toolService->getAddress($this->post);
        return ajaxReturn($result);
    }

    /**
     * 获取城市天气
     * @param Request $request
     * @return JsonResponse
     */
    public function getWeather(Request $request): JsonResponse
    {
        validatePost($request->get('item'), $this->post, ['city_name' => 'required|string']);
        $result = $this->toolService->getWeather($this->post);
        return ajaxReturn($result);
    }
}

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
    public function getImageLists()
    {
        validatePost($this->post, ['name' => 'required|string']);
        $result = $this->imageService->getImageLists($this->post, ['page' => $this->post['page'] ?? 1, 'limit' => $this->post['limit'] ?? 100], ['order' => 'id', 'direction' => 'desc'], ['id as key' , 'href as src']);
        return ajaxReturn($result);
    }
}

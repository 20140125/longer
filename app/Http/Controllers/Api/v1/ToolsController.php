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
}

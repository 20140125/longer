<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\JsonResponse;

class TimeLineController extends BaseController
{
    /**
     * @return JsonResponse
     */
    public function getLists()
    {
        $result = $this->timeLineService->getLists(['page' => 1, 'limit' => 10], ['order' => 'id' ,'direction' => 'desc'], false);
        return ajaxReturn($result);
    }
}

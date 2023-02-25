<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @class TimeLineController
 * @author <fl140125@gmail.com>
 */
class TimeLineController extends BaseController
{
    /**
     * 获取列表
     * @param Request $request
     * @return JsonResponse
     */
    public function getLists(Request $request): JsonResponse
    {
        validatePost($request->get('item'));
        $result = $this->timeLineService->getLists(['page' => $this->post['page'] ?? 1, 'limit' => $this->post['limit'] ?? 10], ['order' => 'id', 'direction' => 'desc'], false);
        return ajaxReturn($result);
    }
}

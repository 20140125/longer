<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @class EmotionController
 * @author <fl140125@gmail.com>
 */
class EmotionController extends BaseController
{
    /**
     * todo：获取列表
     * @param Request $request
     * @return JsonResponse
     */
    public function getLists(Request $request): JsonResponse
    {
        validatePost($request->get('item'), $this->post, ['page' => 'required|integer', 'limit' => 'required|integer', 'type' => 'required|required']);
        $result = $this->emotionService->getLists(['page' => $this->post['page'], 'limit' => $this->post['limit']], $this->post['type'], ['order' => 'id', 'direction' => 'asc'], $this->post['all'] ?? false);
        return ajaxReturn($result);
    }
}

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
        validatePost($request->get('item'));
        $result = $this->emotionService->getLists(['page' => $this->post['page'] ?? 1, 'limit' => $this->post['limit'] ?? 40], $this->post['type'] ?? 1, ['order' => 'id', 'direction' => 'asc'], false);
        return ajaxReturn($result);
    }
}

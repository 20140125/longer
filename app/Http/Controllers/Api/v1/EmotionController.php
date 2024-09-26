<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\JsonResponse;

/**
 * @class EmotionController
 * @author <fl140125@gmail.com>
 */
class EmotionController extends BaseController
{
    /**
     * 获取列表
     * @return JsonResponse
     */
    public function getLists()
    {
        $result = $this->emotionService->getLists(['page' => $this->post['page'] ?? 1, 'limit' => $this->post['limit'] ?? 40], $this->post['type'] ?? 1, ['order' => 'id', 'direction' => 'asc'], false);
        return ajaxReturn($result);
    }
}

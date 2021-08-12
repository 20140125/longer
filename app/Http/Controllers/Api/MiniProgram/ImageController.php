<?php

namespace App\Http\Controllers\Api\MiniProgram;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ImageController extends BaseController
{
    /**
     * todo：获取图片列表
     * @return JsonResponse
     */
    public function getImageType()
    {
        validatePost($this->post, ['parent_id'=>'required|integer']);
        $result = $this->imageService->getImageTypeLists($this->post);
        return ajaxReturn($result);
    }

    /**
     * todo:获取图片列表
     * @return JsonResponse
     */
    public function getImageLists()
    {
        validatePost($this->post, ['page' => 'required|integer', 'limit' => 'required|integer', 'source' => 'required|string']);
        $result = $this->imageService->getImageLists($this->post, ['page' => $this->post['page'], 'limit' => $this->post['limit']]);
        return ajaxReturn($result);
    }

    /**
     * todo:获取最新图片
     * @return JsonResponse
     */
    public function getNewImageLists()
    {
        validatePost($this->post, ['page' => 'required|integer', 'limit' => 'required|integer', 'source' => 'required|string']);
        $result = $this->imageService->getImageLists($this->post, ['page' => $this->post['page'], 'limit' => $this->post['limit']], ['order' => 'rand', 'direction' => 'desc']);
        return ajaxReturn($result);
    }

    /**
     * todo:获取关键词图片
     * @return JsonResponse
     */
    public function getHotImageLists()
    {
        validatePost($this->post, ['page' => 'required|integer', 'limit' => 'required|integer', 'source' => 'required|string', 'name' => 'required|string']);
        $sensitiveKeywords = explode(',', $this->imageService->getSystemConfig('sensitiveKeywords'));
        Log::error(json_encode($sensitiveKeywords, 256));
        if (in_array($this->post['name'], $sensitiveKeywords)) {
            return ajaxReturn(['code' => 20000, 'message' => 'successfully', 'lists' => ['data' => [], 'total' => 0]]);
        }
        $result = $this->imageService->getImageLists($this->post, ['page' => $this->post['page'], 'limit' => $this->post['limit']]);
        return ajaxReturn($result);
    }

    /**
     * todo:获取关键字
     * @return JsonResponse
     */
    public function getHotKeyWords()
    {
        $result = $this->imageService->getHotKeyWords();
        return ajaxReturn($result);
    }
}

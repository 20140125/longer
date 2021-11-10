<?php

namespace App\Http\Controllers\Api\MiniProgram;

use Illuminate\Http\JsonResponse;

class ImageController extends BaseController
{
    /**
     * todo:获取图片列表
     * @return JsonResponse
     */
    public function getImageLists()
    {
        validatePost($this->post, ['page' => 'required|integer', 'limit' => 'required|integer', 'source' => 'required|string']);
        if (empty($this->post['token']) && $this->post['page'] > intval($this->imageService->getSystemConfig('NoLoginMaxPageNum'))) {
            return ajaxReturn(['code' => 20001, 'message' => 'Please Login']);
        }
        $result = $this->imageService->getImageLists($this->post, ['page' => $this->post['page'], 'limit' => $this->post['limit']], ['order' => 'rand', 'direction' => 'desc']);
        return ajaxReturn($result);
    }

    /**
     * todo:获取最新图片
     * @return JsonResponse
     */
    public function getNewImageLists()
    {
        validatePost($this->post, ['page' => 'required|integer', 'limit' => 'required|integer', 'source' => 'required|string']);
        if (empty($this->post['token']) && $this->post['page'] > intval($this->imageService->getSystemConfig('NoLoginMaxPageNum'))) {
            return ajaxReturn(['code' => 20001, 'message' => 'Please Login']);
        }
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
        if (empty($this->post['token']) && $this->post['page'] > intval($this->imageService->getSystemConfig('NoLoginMaxPageNum'))) {
            return ajaxReturn(['code' => 20001, 'message' => 'Please Login']);
        }
        $sensitiveKeywords = explode(',', $this->imageService->getSystemConfig('sensitiveKeywords'));
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
        $result = $this->imageService->getConfiguration(empty($this->post['keywords']) ? 'hotKeyWord' : $this->post['keywords']);
        return ajaxReturn($result);
    }
}

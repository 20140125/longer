<?php

namespace App\Http\Controllers\Api\MiniProgram;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

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
        $sensitiveKeywords = explode(',', $this->imageService->getSystemConfig('SensitiveKeywords'));
        if (!empty($this->post['name']) && in_array($this->post['name'], $sensitiveKeywords)) {
            return ajaxReturn(['code' => 20000, 'message' => 'successfully', 'lists' => ['data' => [], 'total' => 0]]);
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
        $sensitiveKeywords = explode(',', $this->imageService->getSystemConfig('SensitiveKeywords'));
        if (in_array($this->post['name'], $sensitiveKeywords)) {
            return ajaxReturn(['code' => 20000, 'message' => 'successfully', 'lists' => ['data' => [], 'total' => 0]]);
        }
        $result = $this->imageService->getImageLists($this->post, ['page' => $this->post['page'], 'limit' => $this->post['limit']]);
        return ajaxReturn($result);
    }
    /**
     * todo:执行脚本
     * @param Request $request
     * @return JsonResponse|void
     */
    public function runningSpider(Request $request)
    {
        date_default_timezone_set('Asia/Shanghai');
        validatePost($this->post, ['keywords' => 'required|string', 'method' => 'required|string']);
        $_user = $request->get('unauthorized');
        switch ($this->post['method']) {
            case 'syncImageType':
                Artisan::call("longer:sync-spider_image_type {$this->post['keywords']} {$_user->uuid}");
                return ajaxReturn(['code' => 20000, 'message' => 'successfully', 'lists' => [$this->post]]);
            case 'syncImageLists':
                if (is_numeric($this->post['keywords'])) {
                    $keywords = intval($this->post['keywords']);
                    Artisan::call("longer:sync-spider_image_id $keywords {$_user->uuid}");
                } else {
                    Artisan::call("longer:sync-spider_image_url {$this->post['keywords']} {$_user->uuid}");
                }
                return ajaxReturn(['code' => 20000, 'message' => 'successfully', 'lists' => [$this->post]]);
            case 'syncImageSize':
                Artisan::call("longer:sync-spider_image_size {$this->post['keywords']} {$_user->uuid}");
                break;
            case 'syncOauth':
                $rememberToken = !empty($this->post['keywords']) ?  $this->post['keywords'] :  $this->post['remember_token'];
                Artisan::call("longer:sync-oauth $rememberToken {$_user->uuid}");
                return ajaxReturn(['code' => 20000, 'message' => 'successfully', 'lists' => [$this->post]]);
            case 'syncImageListsForTags':
                Artisan::call("longer:sync-spider_image_tag_url {$this->post['keywords']} {$_user->uuid}");
                return ajaxReturn(['code' => 20000, 'message' => 'successfully', 'lists' => [$this->post]]);
            case 'syncSpiderImageSoogif':
                Artisan::call("longer:sync-spider_image_form_soogif {$this->post['keywords']} {$_user->uuid}");
                return ajaxReturn(['code' => 20000, 'message' => 'successfully', 'lists' => [$this->post]]);
        }
    }

    /**
     * todo:获取关键字
     * @return JsonResponse
     */
    public function getHotKeyWords()
    {
        $result = $this->imageService->getConfiguration(empty($this->post['keywords']) ? 'HotKeyWord' : $this->post['keywords']);
        return ajaxReturn($result);
    }
}

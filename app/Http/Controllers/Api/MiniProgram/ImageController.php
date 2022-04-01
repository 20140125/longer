<?php

namespace App\Http\Controllers\Api\MiniProgram;

use App\Http\Controllers\Service\v1\BaseService;
use App\Http\Controllers\Utils\Code;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class ImageController extends BaseController
{
    /**
     * todo:获取图片列表
     * @param Request $request
     * @return JsonResponse
     */
    public function getImageLists(Request $request)
    {
        $start_time = time();
        validatePost($request->get('item'), $this->post, ['page' => 'required|integer', 'limit' => 'required|integer', 'source' => 'required|string']);
        // todo: 单次请求记录超过限制
        if (!empty($this->post['limit']) && $this->post['limit'] > intval((BaseService::getInstance()->getConfiguration('MaxPageLimit', 'ImageBed')[0]))) {
            return ajaxReturn(['code' => Code::ERROR, 'message' => Code::PAGE_SIZE_MESSAGE]);
        }
        if (empty($this->post['token']) && $this->post['page'] > intval($this->imageService->getSystemConfig('NoLoginMaxPageNum'))) {
            return ajaxReturn(['code' => Code::ERROR, 'message' => Code::NOT_LOGIN_MESSAGE]);
        }
        $sensitiveKeywords = explode(',', $this->imageService->getSystemConfig('SensitiveKeywords'));
        if (!empty($this->post['name']) && in_array($this->post['name'], $sensitiveKeywords)) {
            return ajaxReturn(['code' => Code::SUCCESS, 'message' => 'successfully', 'lists' => ['data' => [], 'total' => 0]]);
        }
        $columns = ['href', 'name', 'width', 'height', 'id'];
        $result = $this->imageService->getImageLists($this->post, ['page' => $this->post['page'], 'limit' => $this->post['limit']], ['order' => 'rand', 'direction' => 'desc'], $columns);
        $end_time = time();
        return ajaxReturn($result, (int) $end_time - $start_time);
    }

    /**
     * todo:执行脚本
     * @param Request $request
     * @return JsonResponse
     */
    public function runningSpider(Request $request)
    {
        date_default_timezone_set('Asia/Shanghai');
        validatePost($request->get('item'), $this->post, ['keywords' => 'required|string', 'method' => 'required|string']);
        $_user = $request->get('unauthorized');
        switch ($this->post['method']) {
            case 'syncImageType':
                Artisan::call("longer:sync-spider_image_type {$this->post['keywords']} $_user->uuid");
                break;
            case 'syncImageLists':
                if (is_numeric($this->post['keywords'])) {
                    $keywords = intval($this->post['keywords']);
                    Artisan::call("longer:sync-spider_image_id $keywords $_user->uuid");
                } else {
                    Artisan::call("longer:sync-spider_image_url {$this->post['keywords']} $_user->uuid");
                }
                break;
            case 'syncImageSize':
                Artisan::call("longer:sync-spider_image_size {$this->post['keywords']} $_user->uuid");
                break;
            case 'syncOauth':
                $rememberToken = !empty($this->post['keywords']) ?  $this->post['keywords'] :  $this->post['remember_token'];
                Artisan::call("longer:sync-oauth $rememberToken $_user->uuid");
                break;
            case 'syncImageListsForTags':
                Artisan::call("longer:sync-spider_image_tag_url {$this->post['keywords']} $_user->uuid");
                break;
            case 'syncSpiderImageSoogif':
                Artisan::call("longer:sync-spider_image_form_soogif {$this->post['keywords']} $_user->uuid");
                break;

        }
        return ajaxReturn(['code' => Code::SUCCESS, 'message' => 'successfully', 'lists' => [$this->post]]);
    }

    /**
     * todo:获取关键字
     * @param Request $request
     * @return JsonResponse
     */
    public function getHotKeyWords(Request $request)
    {
        validatePost($request->get('item'));
        $result = $this->imageService->getConfiguration(empty($this->post['keywords']) ? 'HotKeyWord' : $this->post['keywords']);
        return ajaxReturn($result);
    }
}

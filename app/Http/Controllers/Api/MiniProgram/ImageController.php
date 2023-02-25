<?php

namespace App\Http\Controllers\Api\MiniProgram;

use App\Http\Controllers\Service\v1\SystemConfigService;
use App\Http\Controllers\Utils\Code;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class ImageController extends BaseController
{
    /**
     * 获取图片列表
     * @param Request $request
     * @return JsonResponse
     */
    public function getImageLists(Request $request): JsonResponse
    {
        $start_time = microtime(true);
        validatePost($request->get('item'), $this->post, ['page' => 'required|integer', 'limit' => 'required|integer', 'source' => 'required|string']);
        //  单次请求记录超过限制
        if (!empty($this->post['limit']) && $this->post['limit'] > intval((SystemConfigService::getInstance()->getConfiguration('MaxPageLimit', 'ImageBed')[0]))) {
            return ajaxReturn(['code' => Code::ERROR, 'message' => Code::PAGE_SIZE_MESSAGE]);
        }
        if (empty($this->post['token']) && $this->post['page'] > intval($this->imageService->getSystemConfig('NoLoginMaxPageNum'))) {
            return ajaxReturn(['code' => Code::NOT_LOGIN, 'message' => Code::NOT_LOGIN_MESSAGE]);
        }
        $sensitiveKeywords = explode(',', $this->imageService->getSystemConfig('SensitiveKeywords'));
        if (!empty($this->post['name']) && in_array($this->post['name'], $sensitiveKeywords)) {
            return ajaxReturn(['code' => Code::SUCCESS, 'message' => 'successfully', 'lists' => ['data' => [], 'total' => 0]]);
        }
        $columns = ['href', 'name', 'width', 'height', 'id'];
        $result = $this->imageService->getImageLists($this->post, ['page' => $this->post['page'], 'limit' => $this->post['limit']], ['order' => 'rand', 'direction' => 'desc'], $columns);
        $end_time = microtime(true);
        return ajaxReturn($result, ($end_time - $start_time) * 1000);
    }

    /**
     * 执行脚本(待废弃)
     * @param Request $request
     * @return JsonResponse
     */
    public function runningSpider(Request $request): JsonResponse
    {
        date_default_timezone_set('Asia/Shanghai');
        validatePost($request->get('item'), $this->post, ['keywords' => 'required|string', 'method' => 'required|string']);
        return ajaxReturn(['code' => Code::SUCCESS, 'message' => 'successfully', 'lists' => [$this->post]]);
    }

    /**
     * 获取关键字
     * @param Request $request
     * @return JsonResponse
     */
    public function getHotKeyWords(Request $request): JsonResponse
    {
        validatePost($request->get('item'));
        $result = $this->imageService->getConfiguration(empty($this->post['keywords']) ? 'HotKeyWord' : $this->post['keywords']);
        return ajaxReturn($result);
    }
}

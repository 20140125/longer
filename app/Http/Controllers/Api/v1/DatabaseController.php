<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DatabaseController extends BaseController
{
    /**
     * 获取数据表列表
     * @param Request $request
     * @return JsonResponse
     */
    public function getDatabaseLists(Request $request): JsonResponse
    {
        validatePost($request->get('item'));
        $result = $this->databaseService->getDatabaseLists();
        return ajaxReturn($result);
    }

    /**
     * 数据表备份
     * @param Request $request
     * @return JsonResponse
     */
    public function backUpTable(Request $request): JsonResponse
    {
        validatePost($request->get('item'), $this->post, ['name' => 'required|string', 'form' => 'required|string|in:table,source,all']);
        $result = $this->databaseService->backUpTable($this->post);
        return ajaxReturn($result);
    }

    /**
     * TODO：修复数据表
     * @param Request $request
     * @return JsonResponse
     */
    public function repairTable(Request $request): JsonResponse
    {
        validatePost($request->get('item'), $this->post, ['name' => 'required|string']);
        $result = $this->databaseService->repairTable($this->post);
        return ajaxReturn($result);
    }

    /**
     * TODO：优化数据表
     * @param Request $request
     * @return JsonResponse
     */
    public function optimizeTabled(Request $request): JsonResponse
    {
        validatePost($request->get('item'), $this->post, ['name' => 'required|string', 'engine' => 'required|string']);
        $result = $this->databaseService->optimizeTable($this->post);
        return ajaxReturn($result);

    }

    /**
     * TODO：数据表修改
     * @param Request $request
     * @return JsonResponse
     */
    public function alterTable(Request $request): JsonResponse
    {
        validatePost($request->get('item'), $this->post, ['name' => 'required|string', 'comment' => 'required|string']);
        $result = $this->databaseService->commentTable($this->post);
        return ajaxReturn($result);
    }
}

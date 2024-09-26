<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\JsonResponse;

class DatabaseController extends BaseController
{
    /**
     * 获取数据表列表
     * @return JsonResponse
     */
    public function getDatabaseLists()
    {
        $result = $this->databaseService->getDatabaseLists();
        return ajaxReturn($result);
    }

    /**
     * 数据表备份
     * @return JsonResponse
     */
    public function backUpTable()
    {
        validatePost($this->post, ['name' => 'required|string', 'form' => 'required|string|in:table,source,all']);
        $result = $this->databaseService->backUpTable($this->post);
        return ajaxReturn($result);
    }

    /**
     * 修复数据表
     * @return JsonResponse
     */
    public function repairTable()
    {
        validatePost($this->post, ['name' => 'required|string']);
        $result = $this->databaseService->repairTable($this->post);
        return ajaxReturn($result);
    }

    /**
     * 优化数据表
     * @return JsonResponse
     */
    public function optimizeTabled()
    {
        validatePost($this->post, ['name' => 'required|string', 'engine' => 'required|string']);
        $result = $this->databaseService->optimizeTable($this->post);
        return ajaxReturn($result);

    }

    /**
     * 数据表修改
     * @return JsonResponse
     */
    public function alterTable()
    {
        validatePost($this->post, ['name' => 'required|string', 'comment' => 'required|string']);
        $result = $this->databaseService->commentTable($this->post);
        return ajaxReturn($result);
    }
}

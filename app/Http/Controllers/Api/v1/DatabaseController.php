<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\JsonResponse;

class DatabaseController extends BaseController
{
    /**
     * todo:获取数据表列表
     * @return JsonResponse
     */
    public function getDatabaseLists()
    {
        $result = $this->databaseService->getDatabaseLists();
        return ajaxReturn($result);
    }

    /**
     * todo:数据表备份
     * @return JsonResponse
     */
    public function backUpTable()
    {
        validatePost($this->post, ['name'=>'required|string', 'form' => 'required|string|in:table,source,all']);
        $result = $this->databaseService->backUpTable($this->post);
        return ajaxReturn($result);
    }

    /**
     * TODO：修复数据表
     * @param string name 数据库名称
     * @return JsonResponse
     */
    public function repairTable()
    {
        validatePost($this->post, ['name' => 'required|string']);
        $result = $this->databaseService->repairTable($this->post);
        return ajaxReturn($result);
    }

    /**
     * TODO：优化数据表
     * @param string name 数据库名称
     * @return JsonResponse
     */
    public function optimizeTabled()
    {
        validatePost($this->post, ['name' => 'required|string', 'engine' => 'required|string']);
        $result = $this->databaseService->optimizeTable($this->post);
        return ajaxReturn($result);

    }
    /**
     * TODO：数据表修改
     * @param string name 数据库名称
     * @param string common 备注
     * @return JsonResponse
     */
    public function alterTable()
    {
        validatePost($this->post, ['name' => 'required|string','comment' => 'required|string']);
        $result = $this->databaseService->commentTable($this->post);
        return ajaxReturn($result);
    }
}

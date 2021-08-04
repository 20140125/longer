<?php

namespace App\Http\Controllers\Api\v1;


use Illuminate\Http\JsonResponse;

class InterfaceCategoryController extends BaseController
{
    /**
     * todo:获取接口列表
     * @return JsonResponse
     */
    public function categoryLists()
    {
        $result = $this->interfaceCategoryService->getCategoryLists([], ['order' => 'path', 'direction' => 'asc']);
        return ajaxReturn($result);
    }

    /**
     * TODO：保存接口分类
     * @return JsonResponse
     */
    public function saveCategory()
    {
        validatePost($this->post, ['name' => 'required|string', 'pid' => 'required|integer']);
        $result = $this->interfaceCategoryService->saveCategory($this->post);
        return ajaxReturn($result);
    }

    /**
     * todo:更新接口分类
     * @return JsonResponse
     */
    public function updateCategory()
    {
        validatePost($this->post, ['name' => 'required|string', 'pid' => 'required|integer', 'id' => 'required|integer', 'level' => 'required|integer']);
        $result = $this->interfaceCategoryService->updateCategory($this->post);
        return ajaxReturn($result);
    }
    /**
     * todo：删除接口
     * @return JsonResponse
     */
    public function removeCategory()
    {
        validatePost($this->post, ['id' => 'required|integer']);
        $result = $this->interfaceCategoryService->removeCategory($this->post);
        return ajaxReturn($result);
    }
}

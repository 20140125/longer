<?php

namespace App\Http\Controllers\Api\v1;


use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InterfaceCategoryController extends BaseController
{
    /**
     * 获取接口列表
     * @param Request $request
     * @return JsonResponse
     */
    public function categoryLists(Request $request): JsonResponse
    {
        validatePost($request->get('item'));
        $result = $this->interfaceCategoryService->getCategoryLists([], ['order' => 'path', 'direction' => 'asc']);
        return ajaxReturn($result);
    }

    /**
     * 保存接口分类
     * @param Request $request
     * @return JsonResponse
     */
    public function saveCategory(Request $request): JsonResponse
    {
        validatePost($request->get('item'), $this->post, ['name' => 'required|string', 'pid' => 'required|integer']);
        $result = $this->interfaceCategoryService->saveCategory($this->post);
        return ajaxReturn($result);
    }

    /**
     * 更新接口分类
     * @param Request $request
     * @return JsonResponse
     */
    public function updateCategory(Request $request): JsonResponse
    {
        validatePost($request->get('item'), $this->post, ['name' => 'required|string', 'pid' => 'required|integer', 'id' => 'required|integer', 'level' => 'required|integer']);
        $result = $this->interfaceCategoryService->updateCategory($this->post);
        return ajaxReturn($result);
    }

    /**
     * 删除接口
     * @param Request $request
     * @return JsonResponse
     */
    public function removeCategory(Request $request): JsonResponse
    {
        validatePost($request->get('item'), $this->post, ['id' => 'required|integer']);
        $result = $this->interfaceCategoryService->removeCategory($this->post);
        return ajaxReturn($result);
    }
}

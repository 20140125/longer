<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\JsonResponse;

class ApiController extends BaseController
{
    /**
     * todo:获取接口列表
     * @return JsonResponse
     */
    public function interfaceLists()
    {
        $result = $this->apiService->getCategoryLists([], ['order' => 'id', 'direction' => 'asc']);
        return ajaxReturn($result);
    }

    /**
     * todo:获取接口详情
     * @return JsonResponse
     */
    public function getInterface()
    {
        validatePost($this->post, ['id' => 'required|integer', 'source' => 'required|string|in:markdown,text']);
        $result = $this->post['source'] === 'text' ? $this->apiService->getApiList($this->post) : $this->apiService->getMarkDownList($this->post);
        return ajaxReturn($result);
    }

    /**
     * todo:添加接口
     * @return JsonResponse
     */
    public function saveInterface()
    {
        validatePost($this->post, ['source' => 'required|string|in:markdown,text']);
        validatePost($this->post, $this->setRules($this->post['type'], 'save'));
        $result = $this->post['source'] === 'text' ? $this->apiService->saveApiLists($this->post) : $this->apiService->saveMarkDown($this->post);
        return ajaxReturn($result);
    }

    /**
     * todo:更新接口
     * @return JsonResponse
     */
    public function updateInterface()
    {
        validatePost($this->post, ['source' => 'required|string|in:markdown,text']);
        validatePost($this->post, $this->setRules($this->post['source'], 'update'));
        $result = $this->post['source'] === 'text' ? $this->apiService->updateApiLists($this->post) : $this->apiService->updateMarkDown($this->post);
        return ajaxReturn($result);
    }

    /**
     * todo：删除接口
     * @return JsonResponse
     */
    public function removeInterface()
    {
        validatePost($this->post, ['id' => 'required|integer']);
        $result = $this->apiService->removeCategory($this->post);
        return ajaxReturn($result);
    }

    /**
     * todo:设置验证规则
     * @param $source
     * @param $action
     * @return string[]
     */
    private function setRules($source, $action)
    {
        $rules = $source === 'text' ?
            [
                'desc' => 'required|string',
                'type' => 'required|integer',
                'href' => 'required|url',
                'request' => 'required|array',
                'response' => 'required|array',
                'response_string' => 'required|array',
                'remark' => 'required|string'
            ] :
            [
                'html' => 'required|string',
                'markdown' => 'required|string'
            ];
        if ($action === 'update') {
            $rules['id'] = 'required|integer';
        }
        return $rules;
    }
}

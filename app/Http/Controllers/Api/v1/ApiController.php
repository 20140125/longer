<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiController extends BaseController
{
    /**
     * 获取接口详情
     * @return JsonResponse
     */
    public function getInterface()
    {
        validatePost($this->post, ['id' => 'required|integer', 'source' => 'required|string|in:markdown,json']);
        $result = $this->post['source'] === 'json' ? $this->apiService->getApiList($this->post) : $this->apiService->getMarkDownList($this->post);
        return ajaxReturn($result);
    }

    /**
     * 添加接口
     * @return JsonResponse
     */
    public function saveInterface(Request $request)
    {
        validatePost($this->post, ['source' => 'required|string|in:markdown,json']);
        validatePost($this->post, $this->setRules($this->post['source'], 'save'));
        $user = $request->get('unauthorized');
        $result = $this->post['source'] === 'json' ? $this->apiService->saveApiLists($this->post, $user) : $this->apiService->saveMarkDown($this->post, $user);
        return ajaxReturn($result);
    }

    /**
     * 更新接口
     * @return JsonResponse
     */
    public function updateInterface(Request $request)
    {
        validatePost($this->post, ['source' => 'required|string|in:markdown,json']);
        validatePost($this->post, $this->setRules($this->post['source'], 'update'));
        $user = $request->get('unauthorized');
        $result = $this->post['source'] === 'json' ? $this->apiService->updateApiLists($this->post, $user) : $this->apiService->updateMarkDown($this->post, $user);
        return ajaxReturn($result);
    }

    /**
     * 设置验证规则
     * @param $source
     * @param $action
     * @return string[]
     */
    private function setRules($source, $action)
    {
        $rules = $source === 'json' ?
            [
                'desc'            => 'required|string',
                'api_id'          => 'required|integer',
                'href'            => 'required|url',
                'request'         => 'required|array',
                'response'        => 'required|array',
                'response_string' => 'required|array',
                'remark'          => 'required|string'
            ] :
            [
                'html'     => 'required|string',
                'markdown' => 'required|string'
            ];
        if ($action === 'update') {
            $rules['id'] = 'required|integer';
        }
        return $rules;
    }
}

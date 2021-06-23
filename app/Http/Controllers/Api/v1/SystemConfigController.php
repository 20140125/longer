<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SystemConfigController extends BaseController
{
    /**
     * todo:获取系统配置列表
     * @return JsonResponse
     */
    public function getSystemConfigLists()
    {
        validatePost($this->post, ['page' => 'required|integer', 'limit' => 'required|integer']);
        $result = $this->systemConfigService->getSystemConfigLists(['page' => $this->post['page'], 'limit' => $this->post['limit']]);
        return ajaxReturn($result);
    }

    /**
     * todo:获取系统配置
     * @param Request $request
     * @return JsonResponse
     */
    public function getConfig(Request $request)
    {
        validatePost($this->post, ['name' => 'required|string', 'login' => 'required|string|in:before,after']);
        $_user = $request->get('unauthorized');
        if (!$_user) {
            $this->post['name'] = 'Oauth';
        }
        $result = $this->systemConfigService->getConfig($this->post);
        return ajaxReturn($result);
    }

    public function saveSystemConfig()
    {
        validatePost($this->post, ['name'=>'required|string|unique:os_config']);
        $result = $this->systemConfigService->saveSystemConfig($this->post);
        return ajaxReturn($result);
    }
}

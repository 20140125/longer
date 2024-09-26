<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SystemConfigController extends BaseController
{
    /**
     * 获取系统配置列表
     * @return JsonResponse
     */
    public function getSystemConfigLists()
    {
        validatePost($this->post, ['page' => 'required|integer', 'limit' => 'required|integer']);
        $result = $this->systemConfigService->getSystemConfigLists(['page' => $this->post['page'], 'limit' => $this->post['limit']]);
        return ajaxReturn($result);
    }

    /**
     * 获取系统配置(登录态或未登录)
     * @param Request $request
     * @return JsonResponse
     */
    public function getSystemConfig(Request $request)
    {
        validatePost($this->post, ['name' => 'required|string']);
        $user = $request->get('unauthorized');
        $result = $this->systemConfigService->getConfig($this->post, $user);
        return ajaxReturn($result);
    }

    /**
     * 保存系统配置
     * @return JsonResponse
     */
    public function saveSystemConfig()
    {
        validatePost($this->post, ['name' => 'required|string|unique:os_system_config', 'children' => 'required|array', 'status' => 'required|integer|in:1,2']);
        $result = $this->systemConfigService->saveSystemConfig($this->post);
        return ajaxReturn($result);
    }

    /**
     * 更新系统配置
     * @return JsonResponse
     */
    public function updateSystemConfig()
    {
        validatePost($this->post, ['name' => 'required|string', 'children' => 'required|array', 'status' => 'required|integer|in:1,2']);
        $result = $this->systemConfigService->updateSystemConfig($this->post);
        return ajaxReturn($result);
    }
}

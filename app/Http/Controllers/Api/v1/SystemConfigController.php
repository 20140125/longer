<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SystemConfigController extends BaseController
{
    /**
     * todo:获取系统配置列表
     * @param Request $request
     * @return JsonResponse
     */
    public function getSystemConfigLists(Request $request): JsonResponse
    {
        validatePost($request->get('item'), $this->post, ['page' => 'required|integer', 'limit' => 'required|integer']);
        $result = $this->systemConfigService->getSystemConfigLists(['page' => $this->post['page'], 'limit' => $this->post['limit']]);
        return ajaxReturn($result);
    }

    /**
     * todo:获取系统配置(登录态或未登录)
     * @param Request $request
     * @return JsonResponse
     */
    public function getSystemConfig(Request $request): JsonResponse
    {
        validatePost($request->get('item'), $this->post, ['name' => 'required|string']);
        $result = $this->systemConfigService->getConfig($this->post, $request->get('unauthorized'));
        return ajaxReturn($result);
    }

    /**
     * todo:保存系统配置
     * @return JsonResponse
     */
    public function saveSystemConfig(Request $request): JsonResponse
    {
        validatePost($request->get('item'), $this->post, ['name' => 'required|string|unique:os_system_config', 'children' => 'required|array', 'status' => 'required|integer|in:1,2']);
        $result = $this->systemConfigService->saveSystemConfig($this->post);
        return ajaxReturn($result);
    }

    /**
     * todo:更新系统配置
     * @param Request $request
     * @return JsonResponse
     */
    public function updateSystemConfig(Request $request): JsonResponse
    {
        validatePost($request->get('item'), $this->post, ['id' => 'required|integer', 'status' => 'required|integer|in:1,2']);
        $result = $this->systemConfigService->updateSystemConfig($this->post);
        return ajaxReturn($result);
    }

    /**
     * todo:插件安装、卸载
     * @param Request $request
     * @return JsonResponse
     */
    public function pluginAction(Request $request): JsonResponse
    {
        validatePost($request->get('item'), $this->post, ['pid' => 'required|integer', 'status' => 'required|integer|in:1,2']);
        $result = $this->systemConfigService->pluginAction($this->post);
        return ajaxReturn($result);
    }
}

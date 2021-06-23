<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Service\v1\RoleService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RequestAuthController extends BaseController
{
    /**
     * todo:获取申请权限列表
     * @param Request $request
     * @return JsonResponse
     */
    public function getRequestAuthLists(Request $request)
    {
        validatePost($this->post, ['page' => 'required|integer', 'limit' => 'required|integer']);
        $_user = $request->get('unauthorized');
        $result = $this->requestAuthService->getRequestAuthLists($_user, [], ['page' => $this->post['page'], 'limit' => $this->post['limit']]);
        return ajaxReturn($result);
    }

    /**
     * todo:获取用户权限
     * @return JsonResponse
     */
    public function getUserAuth()
    {
        validatePost($this->post, ['user_id' => 'required']);
        $result = $this->requestAuthService->getUserAuth(is_numeric($this->post['user_id']) ? $this->post['user_id'] : decrypt($this->post['user_id']));
        return ajaxReturn($result);
    }

    /**
     * todo:添加申请权限
     * @return JsonResponse
     */
    public function saveRequestAuth()
    {
        validatePost($this->post, [
            'user_id' => 'required|string',
            'expires' => 'required|date|before:'.date('Y-m-d H:i:s', strtotime('+1 year')).'|after:'.date('Y-m-d H:i:s'),
            'desc' => 'required|string',
            'href' => 'required|string'
        ]);
        Log::error(json_encode($this->post));
        $result = $this->requestAuthService->saveRequestAuth($this->post);
        return ajaxReturn($result);
    }

    /**
     * todo:更新申请权限
     * @return JsonResponse
     */
    public function updateRequestAuth()
    {
        $rules = ['status' => 'required|integer|in:1,2', 'id' => 'required|integer'];
        if (empty($this->post['act'])) {
            $rules['username'] = 'required|string';
            $rules['href'] = 'required|string';
            $rules['created_at'] = 'required|date';
            $rules['expires'] = 'required|date|before:'.date('Y-m-d H:i:s', strtotime('+1 year')).'|after:'.date('Y-m-d H:i:s');
        }
        validatePost($this->post, $rules);
        $result = (int)$this->post['status'] === 2 ?  $this->requestAuthService->removeRequestAuth($this->post) : $this->requestAuthService->updateRequestAuth($this->post);
        return ajaxReturn($result);
    }
}

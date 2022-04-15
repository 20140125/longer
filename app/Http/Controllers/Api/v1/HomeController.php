<?php

namespace App\Http\Controllers\Api\v1;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomeController extends BaseController
{
    /**
     * todo：获取导航栏
     * @return JsonResponse
     */
    public function getMenu(Request $request)
    {
        $user = $request->get('unauthorized');
        $result = json_decode(Cache::get('role_permission_' . $user->role_id ?? 2), true);
        if (empty($result['lists'])) {
            $result = $this->authService->getLists(array('role_id' => $user->role_id ?? 2, 'id' => ''), ['id', 'pid', 'name', 'href']);
            Cache::put('role_permission_' . $user->role_id, json_encode($result, JSON_UNESCAPED_UNICODE), Carbon::now()->addHour());
        }
        return ajaxReturn($result);
    }
}

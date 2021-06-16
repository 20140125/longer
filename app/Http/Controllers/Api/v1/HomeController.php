<?php

namespace App\Http\Controllers\Api\v1;

    use App\Http\Controllers\Utils\Code;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HomeController extends BaseController
{
    /**
     * todo：获取导航栏
     * @return JsonResponse
     */
    public function getMenu(Request $request)
    {
        $user = $request->get('unauthorized');
        $result = $this->authService->getLists(array('role_id' => $user->role_id, 'id' => ''));
        return ajaxReturn($result);
    }
}

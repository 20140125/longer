<?php

namespace App\Http\Controllers\Api\v1;

    use App\Http\Controllers\Utils\Code;
    use Carbon\Carbon;
    use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Cache;
    use Illuminate\Support\Facades\Log;

    class HomeController extends BaseController
{
    /**
     * todo：获取导航栏
     * @return JsonResponse
     */
    public function getMenu(Request $request)
    {
        $user = $request->get('unauthorized');
        $result = json_decode(Cache::get('role_'.$user->role_id), true);
        if (empty($result['lists'])) {
            $result = $this->authService->getLists(array('role_id' => $user->role_id, 'id' => ''));
            Cache::put('role_'.$user->role_id, json_encode($result, JSON_UNESCAPED_UNICODE), Carbon::now()->addHour());
        }
        return ajaxReturn($result);
    }
}

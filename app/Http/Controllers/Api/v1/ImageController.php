<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ImageController extends BaseController
{
    /**
     * TODO:：图床列表
     * @param integer id
     * @return JsonResponse
     */
    public function bed()
    {
        if (empty($this->post['id'])) {
            $lists = getTree(DB::table('os_soogif_type')->get(['name','id','pid']), '0', 'children');
            return $this->ajaxReturn(Code::SUCCESS, 'successfully', $lists);
        }
        $this->validatePost(['id'=>'required|integer']);
        $lists['data'] = DB::table('os_soogif')
            ->where('type', '=', $this->post['id'])
            ->get(['id','name as label','type','href as url']);
        $lists['total'] =  DB::table('os_soogif')->where('type', '=', $this->post['id'])->count();
        return $this->ajaxReturn(Code::SUCCESS, 'successfully', $lists);
    }
}

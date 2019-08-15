<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;
use App\Models\Area;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Class AreaController
 * @author <fl140125@gmail.com>
 * @package App\Http\Controllers\Api\v1
 */
class AreaController extends BaseController
{
    /**
     * @var Area $areaModel
     */
    protected $areaModel;
    /**
     * AreaController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->areaModel = Area::getInstance();
    }

    /**
     * TODO:获取城市列表
     * @return JsonResponse
     */
    public function index()
    {
        $result = Cache::get('area');
        if (empty($result)) {
            $result = $this->areaModel->getResultLists(['id','parent_id as pid','name','code','info']);
            Cache::forever('area',$result);
        }
        return $this->ajax_return(Code::SUCCESS,'successfully',$result);
    }
}

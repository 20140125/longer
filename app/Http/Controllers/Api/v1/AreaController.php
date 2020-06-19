<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Amap;
use App\Http\Controllers\Utils\Code;
use App\Models\Area;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

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
     * @var Amap $amapControl
     */
    protected $amapControl;

    /**
     * AreaController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->areaModel = Area::getInstance();
        $this->amapControl = Amap::getInstance();
    }
    /**
     * TODO:获取城市列表
     * @param integer parent_id 上级ID
     * @return JsonResponse
     */
    public function index()
    {
        $this->validatePost(['parent_id'=>'required|integer|exists:os_china_area']);
        $result = $this->areaModel->getResultLists($this->post['parent_id'],['id','parent_id as pid','name','code','info']);
        foreach ($result as &$item){
            $item->hasChildren = false;
            if ($this->areaModel->getResult('parent_id',$item->id)) {
                $item->hasChildren = true;
            }
        }
        return $this->ajax_return(Code::SUCCESS,'successfully',$result);
    }

    /**
     * TODO:获取天气
     * @param string code 城市码
     * @param integer id ID
     * @return JsonResponse
     */
    public function weather()
    {
        try {
            $this->validatePost(['code'=>'required|string|exists:os_china_area','id'=>'required|integer|exists:os_china_area']);
            $result = object_to_array($this->amapControl->getWeather($this->post['code'],'all'));
            if (!empty($result)){
                $forecast = $result['info'] == 'OK' ? $result['forecasts'][0] : '';
                $info =  [
                    'city' => $forecast['city'],
                    'adcode' => $forecast['adcode'],
                    'province' => $forecast['province'],
                    'reporttime' => $forecast['reporttime'],
                    'casts' => $forecast['casts'] ? $forecast['casts'][0] : ''
                ];
                $this->areaModel->updateResult(['info'=>json_encode($info,JSON_UNESCAPED_UNICODE),'forecast'=>json_encode($forecast,JSON_UNESCAPED_UNICODE)],'code',$this->post['code']);
                $this->redisClient->setValue($this->post['code'],['info'=>json_encode($info,JSON_UNESCAPED_UNICODE),'forecast'=>json_encode($forecast,JSON_UNESCAPED_UNICODE)],['EX'=>3600]);
                return $this->ajax_return(Code::SUCCESS,'get weather successfully',$info);
            }
            return $this->ajax_return(Code::ERROR,'get weather failed',$result);
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
        }
    }

    /**
     * TODO:城市列表
     * @return JsonResponse
     */
    public function lists()
    {
        $result = Cache::get('city');
        if (empty($result)) {
            $result = get_tree($this->areaModel->getAll(),1,'children','parent_id');
            Cache::forever('city',$result);
        }
        return $this->ajax_return(Code::SUCCESS,'successfully',$result);
    }
}

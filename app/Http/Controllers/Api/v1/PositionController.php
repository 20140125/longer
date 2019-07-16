<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;
use App\Models\Position;
use App\Models\Region;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * 地区
 * Class PositionController
 * @package App\Http\Controllers\Api\v1
 */
class PositionController extends BaseController
{
    /**
     * @var Position $positionModel 地区模型
     * @var Region $regionModel 地区模型
     */
    protected $positionModel,$regionModel;

    /**
     * 构造函数
     * PositionController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->regionModel = Region::getInstance();
        $this->positionModel = Position::getInstance();
    }

    /**
     * 地区列表
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        if ($request->isMethod('get')){
            return $this->ajax_return(Code::METHOD_ERROR,'error');
        }
        $result = $this->positionModel->getLists($this->post['level'],$this->post['code_id'],$this->post['status']);
        return $this->ajax_return(Code::SUCCESS,'success',$result);
    }

    /**
     * 三级联动地区
     * @param Request $request
     * @return JsonResponse
     */
    public function tools(Request $request)
    {
        if ($request->isMethod('get')){
            return $this->ajax_return(Code::METHOD_ERROR,'error');
        }
        switch ($this->post['postId']){
            case '1':
                $region = $this->regionModel->getLists($this->post['field']??'region_type',$this->post['value']??'1');
                return $this->ajax_return(Code::SUCCESS,'success',$region);
                break;
            case '2':
                $region = $this->positionModel->getLists2($this->post['field']??'parent_code_id',$this->post['value']??'100000000000');
                return $this->ajax_return(Code::SUCCESS,'success',$region);
                break;
            case '3':
                $region = $this->positionModel->getLists3($this->post['field']??'',$this->post['value']??'','=',['*'],$this->post['table']??'province');
                return $this->ajax_return(Code::SUCCESS,'success',$region);
                break;
            default:
                return $this->ajax_return(Code::METHOD_ERROR,'error');
        }

    }
}

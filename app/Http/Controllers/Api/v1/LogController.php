<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;
use App\Models\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * 系统日志管理
 * Class LogController
 * @package App\Http\Controllers\Api\v1
 */
class LogController extends BaseController
{
    /**
     * @var Log $logModel 日志模型
     */
    protected $logModel;

    /**
     * LogController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->logModel = Log::getInstance();
    }

    /**
     * 日志列表
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        if ($request->isMethod('get')){
            return $this->ajax_return(Code::METHOD_ERROR,'error');
        }
        $result = $this->logModel->getLists($this->post['page'],$this->post['limit'],'');
        foreach ($result['data'] as &$item){
            $item->created_at = date("Y-m-d H:i:s",$item->created_at);
        }
        return $this->ajax_return(Code::SUCCESS,'success',$result);
    }

    /**
     * @todo :日志保存
     * @param Request $request
     * @return JsonResponse
     */
    public function save(Request $request)
    {
        if ($request->isMethod('get')){
            return $this->ajax_return(Code::METHOD_ERROR,'error');
        }
        $this->post['username'] = $this->adminUserModel->getResult('remember_token',$this->post['token'])->username;
        $result = act_log($this->post);
        return $this->ajax_return(Code::SUCCESS,'success',$result);
    }

    /**
     * 删除日志
     * @param Request $request
     * @return JsonResponse
     */
    public function delete(Request $request)
    {
        if ($request->isMethod('get')){
            return $this->ajax_return(Code::METHOD_ERROR,'error');
        }
        $result = $this->logModel->deleteResult('id',$this->post['id'],'=');
        if (!empty($result)){
            return $this->ajax_return(Code::SUCCESS,'delete log success');
        }
        return $this->ajax_return(Code::ERROR,'delete log error');
    }
}

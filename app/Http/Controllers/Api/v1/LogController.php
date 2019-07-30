<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;
use App\Models\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * todo 系统日志管理
 * Class LogController
 * @author <fl140125@gmail.com>
 * @package App\Http\Controllers\Api\v1
 */
class LogController extends BaseController
{
    /**
     * @var Log $logModel 日志模型
     */
    protected $logModel;

    /**
     * todo LogController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->logModel = Log::getInstance();
    }

    /**
     * todo 日志列表
     * @return JsonResponse
     */
    public function index()
    {
        $result = $this->logModel->getLists($this->post['page'],$this->post['limit'],'');
        foreach ($result['data'] as &$item){
            $item->created_at = date("Y-m-d H:i:s",$item->created_at);
        }
        return $this->ajax_return(Code::SUCCESS,'successfully',$result);
    }

    /**
     * @todo :日志保存
     * @return JsonResponse
     */
    public function save()
    {
        $this->post['username'] = $this->userModel->getResult('remember_token',$this->post['token'])->username ?? $this->oauthModel->getResult('remember_token',$this->post['token'])->username;
        $result = act_log($this->post);
        return $this->ajax_return(Code::SUCCESS,'save log successfully',$result);
    }

    /**
     * todo 删除日志
     * @return JsonResponse
     */
    public function delete()
    {
        $result = $this->logModel->deleteResult('id',$this->post['id'],'=');
        if (!empty($result)){
            return $this->ajax_return(Code::SUCCESS,'delete log successfully');
        }
        return $this->ajax_return(Code::ERROR,'delete log error');
    }
}

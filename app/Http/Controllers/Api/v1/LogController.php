<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;
use App\Models\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * TODO: 系统日志管理
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
     * TODO: LogController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->logModel = Log::getInstance();
    }

    /**
     * TODO: 日志列表
     * @param integer page
     * @param integer limit
     * @return JsonResponse
     */
    public function index()
    {
        $this->validatePost(['page'=>'required|integer|min:1','limit'=>'required|integer|min:15']);
        $result = $this->logModel->getLists($this->post['page']??1,$this->post['limit']??15,'');
        foreach ($result['data'] as &$item){
            $item->created_at = date("Y-m-d H:i:s",$item->created_at);
        }
        return $this->ajax_return(Code::SUCCESS,'successfully',$result);
    }

    /**
     * TODO: :日志保存
     * @return JsonResponse
     */
    public function save()
    {
        $this->post['username'] = $this->userModel->getResult('remember_token',$this->post['token'])->username ?? $this->oauthModel->getResult('remember_token',$this->post['token'])->username;
        $result = act_log($this->post);
        return $this->ajax_return(Code::SUCCESS,'save log successfully',$result);
    }

    /**
     * TODO: 删除日志
     * @param integer id
     * @return JsonResponse
     */
    public function delete()
    {
        $this->validatePost(['id'=>'required|integer|min:1']);
        $result = $this->logModel->deleteResult('id',$this->post['id'],'=');
        if (!empty($result)){
            return $this->ajax_return(Code::SUCCESS,'delete log successfully');
        }
        return $this->ajax_return(Code::ERROR,'delete log error');
    }
}

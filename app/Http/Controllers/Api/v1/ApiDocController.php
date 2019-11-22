<?php
namespace App\Http\Controllers\Api\v1;
use App\Http\Controllers\Utils\Code;
use App\Models\ApiCategory;
use App\Models\ApiDocLists;
use App\Models\ApiLists;
use App\Models\ApiLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

/**
 * api列表
 * @author <fl140125@gmail.com>
 * Class ApiDocController
 * @package App\Http\Controllers\Api\v1
 */
class ApiDocController extends BaseController
{
    /**
     * @var ApiLists $apiListsModel api列表模型
     * @var ApiLog $apiLogModel api日志模型
     */
    protected $apiDocListsModel,$apiLogModel;

    /**
     * todo 构造函数
     * ApiDocController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->apiDocListsModel = ApiDocLists::getInstance();
        $this->apiLogModel = ApiLog::getInstance();
    }
    /*********************************************************************api 列表****************************************************************/
    /**
     * api 列表
     * @return JsonResponse
     */
    public function index()
    {
        $this->validatePost(['type'=>'required|integer']);
        $result = $this->apiDocListsModel->getResult('type',$this->post['type']);
        if (empty($result)){
            return $this->ajax_return(Code::ERROR,'interface not found');
        }
        return $this->ajax_return(Code::SUCCESS,'successfully',$result);
    }
    /**
     * todo：保存API数据
     * @return JsonResponse
     */
    public function save()
    {
        $this->validatePost(['markdown' =>'required', 'html' =>'required', 'type' =>'required|integer',]);
        $result = $this->apiDocListsModel->addResult($this->post);
        if (!empty($result)){
            $apiLog = array(
                'username' => $this->users->username,
                'api_id' => $result,
                'updated_at' => time(),
                'type' => 2,
                'markdown' => $this->post['markdown'],
                'json' => '',
                'desc' => '编辑'.ApiCategory::getInstance()->getResult('id',$this->post['type'])->name
            );
            $this->apiLogModel->addResult($apiLog);
            return $this->ajax_return(Code::SUCCESS,'save api doc successfully');
        }
        return $this->ajax_return(Code::ERROR,'save api doc error');
    }

    /**
     * todo 更新API数据
     * @return JsonResponse
     */
    public function update()
    {
        $this->validatePost(['markdown' =>'required', 'html' =>'required', 'type' =>'required|integer',]);
        $result = $this->apiDocListsModel->updateResult($this->post,'id',$this->post['id']);
        if (!empty($result)){
            $apiLog = array(
                'username' => $this->users->username,
                'api_id' => $this->post['id'],
                'updated_at' => time(),
                'type' => 2,
                'markdown' => $this->post['markdown'],
                'json' => '',
                'desc' => '编辑'.ApiCategory::getInstance()->getResult('id',$this->post['type'])->name
            );
            $this->apiLogModel->addResult($apiLog);
            return $this->ajax_return(Code::SUCCESS,'update api doc successfully');
        }
        return $this->ajax_return(Code::ERROR,'update api doc error');
    }
}

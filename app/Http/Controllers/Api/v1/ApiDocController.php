<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;
use App\Models\ApiCategory;
use App\Models\ApiDocLists;
use App\Models\ApiLists;
use App\Models\ApiLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * api列表
 * @author <fl140125@gmail.com>
 * Class ApiDocController
 * @package App\Http\Controllers\Api\v1
 */
class ApiDocController extends BaseController
{
    /**
     * @var ApiDocLists $apiDocListsModel api列表模型
     */
    protected $apiDocListsModel;
    /**
     * @var ApiLog $apiLogModel api日志模型
     */
    protected $apiLogModel;

    /**
     * TODO: 构造函数
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
     * @param integer type 接口ID
     * @return JsonResponse
     */
    public function index()
    {
        $this->validatePost(['type'=>'required|integer']);
        $result = $this->apiDocListsModel->getResult('type', $this->post['type']);
        return empty($result) ? $this->ajaxReturn(Code::ERROR, 'interface not found')
            : $this->ajaxReturn(Code::SUCCESS, 'successfully', $result);
    }
    /**
     * TODO:：保存API数据
     * @param integer type 分类ID
     * @param string html html详情
     * @param string markdown详情
     * @return JsonResponse
     */
    public function save()
    {
        $this->validatePost(['markdown' =>'required|string', 'html' =>'required|string', 'type' =>'required|integer',]);
        $result = $this->apiDocListsModel->addResult($this->post);
        if (!empty($result)) {
            $apiLog = array(
                'username' => $this->users->username,
                'api_id' => $result,
                'updated_at' => time(),
                'type' => 2,
                'markdown' => $this->post['markdown'],
                'json' => '',
                'desc' => '编辑'.ApiCategory::getInstance()->getResult('id', $this->post['type'])->name
            );
            $this->apiLogModel->addResult($apiLog);
            return $this->ajaxReturn(Code::SUCCESS, 'save api doc successfully');
        }
        return $this->ajaxReturn(Code::ERROR, 'save api doc error');
    }

    /**
     * TODO: 更新API数据
     * @param integer type 分类ID
     * @param string html html详情
     * @param string markdown详情
     * @param integer id 接口ID
     * @return JsonResponse
     */
    public function update()
    {
        $this->validatePost(
            ['markdown' =>'required|string', 'html' =>'required|string',
             'type' =>'required|integer','id'=>'required|integer']
        );
        $result = $this->apiDocListsModel->updateResult($this->post, 'id', $this->post['id']);
        if (!empty($result)) {
            $apiLog = array(
                'username' => $this->users->username,
                'api_id' => $this->post['id'],
                'updated_at' => time(),
                'type' => 2,
                'markdown' => $this->post['markdown'],
                'json' => '',
                'desc' => '编辑'.ApiCategory::getInstance()->getResult('id', $this->post['type'])->name
            );
            $this->apiLogModel->addResult($apiLog);
            return $this->ajaxReturn(Code::SUCCESS, 'update api doc successfully');
        }
        return $this->ajaxReturn(Code::ERROR, 'update api doc error');
    }
}

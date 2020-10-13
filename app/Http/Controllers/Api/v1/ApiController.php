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
 * Class ApiController
 * @package App\Http\Controllers\Api\v1
 */
class ApiController extends BaseController
{
    /**
     * @var ApiCategory $apiCategoryModel api分类模型
     * @var ApiLists $apiListsModel api列表模型
     * @var ApiLog $apiLogModel api日志模型
     */
    protected $apiCategoryModel,$apiListsModel,$apiLogModel;

    /**
     * TODO: 构造函数
     * ApiController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->apiListsModel = ApiLists::getInstance();
        $this->apiCategoryModel = ApiCategory::getInstance();
        $this->apiLogModel = ApiLog::getInstance();
    }
    /*********************************************************************api 列表****************************************************************/
    /**
     * TODO:：api接口详情
     * @param integer type
     * @return JsonResponse
     */
    public function index()
    {
        $this->validatePost(['type'=>'required|integer']);
        $result = $this->apiListsModel->getResult('type',$this->post['type']);
        if (empty($result)){
            return $this->ajax_return(Code::ERROR,'interface not found');
        }
        return $this->ajax_return(Code::SUCCESS,'successfully',$result);
    }

    /**
     * TODO:：接口分类
     * @return JsonResponse
     */
    public function category()
    {
        $result = $this->apiCategoryModel->getResultListsLevel2();
        return $this->ajax_return(Code::SUCCESS,'successfully', ['category_tree'=>get_tree($result,0,'children'),'category'=>$result]);
    }
    /**
     * TODO:：保存API数据
     * @param String desc 接口描述
     * @param Integer type 分类ID
     * @param String href 接口地址
     * @param String method 请求方法
     * @param String request 请求参数说明
     * @param String response 返回参数说明
     * @param String response_string 接口详情
     * @param String remark 备注
     * @return JsonResponse
     */
    public function save()
    {
        $this->validatePost($this->rules('lists'));
        $result = $this->apiListsModel->addResult($this->post);
        if (!empty($result)){
            $apiLog = array(
                'username' => $this->users->username,
                'api_id' => $result,
                'updated_at' => time(),
                'type' => 1,
                'json' => json_encode($this->post,JSON_UNESCAPED_UNICODE),
                'markdown' => '',
                'desc' => '编辑'.$this->post['desc']
            );
            $this->apiLogModel->addResult($apiLog);
            return $this->ajax_return(Code::SUCCESS,'save api lists successfully');
        }
        return $this->ajax_return(Code::ERROR,'save api lists error');
    }

    /**
     * TODO: 更新API数据
     * @param integer id 接口ID
     * @param String desc 接口描述
     * @param Integer type 分类ID
     * @param String href 接口地址
     * @param String method 请求方法
     * @param String request 请求参数说明
     * @param String response 返回参数说明
     * @param String response_string 接口详情
     * @param String remark 备注
     * @return JsonResponse
     */
    public function update()
    {
        $this->validatePost($this->rules('lists'));
        $result = $this->apiListsModel->updateResult($this->post,'id',$this->post['id']);
        if (!empty($result)){
            $apiLog = array(
                'username' => $this->users->username,
                'api_id' => $result,
                'updated_at' => time(),
                'type' => 1,
                'json' => json_encode($this->post,JSON_UNESCAPED_UNICODE),
                'markdown' => '',
                'desc' => '编辑'.$this->post['desc']
            );
            $this->apiLogModel->addResult($apiLog);
            return $this->ajax_return(Code::SUCCESS,'update api lists successfully');
        }
        return $this->ajax_return(Code::ERROR,'update api lists error');
    }
    /*********************************************************************api 分类****************************************************************/

    /**
     * TODO:：保存APICategory数据
     * @param String name 分类名称
     * @param integer pid 上级ID
     * @param String path 路径
     * @param integer level 层级
     * @return JsonResponse
     */
    public function categorySave()
    {
        $this->validatePost($this->rules('category'));
        $result = $this->apiCategoryModel->addResult($this->post);
        if (!empty($result)){
            return $this->ajax_return(Code::SUCCESS,'save api category successfully');
        }
        return $this->ajax_return(Code::ERROR,'save api category error');
    }
    /**
     * TODO: 更新APICategory数据
     * @param integer id 分类ID
     * @param String name 分类名称
     * @param integer pid 上级ID
     * @param String path 路径
     * @param integer level 层级
     * @return JsonResponse
     */
    public function categoryUpdate()
    {
        $this->validatePost($this->rules('category'));
        unset($this->post['children']);
        $result = $this->apiCategoryModel->updateResult($this->post,'id',$this->post['pid']);
        if (!empty($result)){
            return $this->ajax_return(Code::SUCCESS,'update api category successfully');
        }
        return $this->ajax_return( Code::ERROR,'update api category error');
    }

    /**
     * TODO: 删除api分类
     * @param integer id 分类ID
     * @return JsonResponse
     */
    public function CategoryDelete()
    {
        $this->validatePost(['id'=>'required|integer']);
        $result = $this->apiCategoryModel->deleteResult('id',$this->post['id']);
        if (!empty($result)){
            $this->apiListsModel->deleteResult('type',$this->post['id']);
            ApiDocLists::getInstance()->deleteResult('type',$this->post['id']);
            return $this->ajax_return(Code::SUCCESS,'remove api and api category successfully');
        }
        return $this->ajax_return(Code::ERROR,'remove api and api category errors');
    }

    /**
     * 定义规则
     * @param $methods
     * @return array
     */
    protected function rules($methods)
    {
        $rules = array();
        switch ($methods){
            case 'lists':
                $rules = [
                    'desc' =>'required',
                    'method' =>'required|in:POST,GET,DELETE,PUT',
                    'href' =>'required|url',
                    'response_string' =>'required|Array',
                    'type' =>'required|integer',
                ];
                break;
            case 'category':
                $rules = [
                    'name' =>'required',
                ];
                break;
        }
        return $rules;
    }
}

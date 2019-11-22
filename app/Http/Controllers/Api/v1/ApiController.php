<?php
namespace App\Http\Controllers\Api\v1;
use App\Http\Controllers\Utils\Code;
use App\Models\ApiCategory;
use App\Models\ApiLists;
use App\Models\ApiLog;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;

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
     * todo 构造函数
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
     * api 列表
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
     * todo：接口分类
     * @return JsonResponse
     */
    public function category()
    {
        $result = $this->apiCategoryModel->getResultListsLevel2();
        return $this->ajax_return(Code::SUCCESS,'successfully', ['category_tree'=>get_tree($result,0,'children'),'category'=>$result]);
    }
    /**
     * todo：保存API数据
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
     * todo 更新API数据
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
     * todo：保存APICategory数据
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
     * todo 更新APICategory数据
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
     * todo 删除api分类
     * @return JsonResponse
     */
    public function CategoryDelete()
    {
        $this->validatePost(['id'=>'required|integer']);
        $result = $this->apiCategoryModel->deleteResult('id',$this->post['id']);
        if (!empty($result)){
            $this->apiListsModel->deleteResult('type',$this->post['id']);
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
                    'response_string' =>'required|json',
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

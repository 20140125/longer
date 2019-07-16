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
     * 构造函数
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

    /**
     * todo：api 分类列表
     * @return Collection
     */
    protected function getCategory()
    {
        $result = $this->apiCategoryModel->getResultListsLevel2();
        return $result;
    }
    /*********************************************************************api 列表****************************************************************/
    /**
     * api 列表
     * @param Request $request
     * @return JsonResponse
     */

    public function index(Request $request)
    {
        if ($request->isMethod('get')){
            return $this->$this->ajax_return(Code::METHOD_ERROR,'error');
        }
        $result = $this->apiListsModel->getResult('type',$this->post['type']);
        if (empty($result)){
            return ajax_return(Code::ERROR,'interface not found');
        }
        return ajax_return(Code::SUCCESS,'success','',$result);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function category(Request $request)
    {
        if ($request->isMethod('get')){
            return $this->$this->ajax_return(Code::METHOD_ERROR,'error');
        }
        $result = $this->apiCategoryModel->getResultListsLevel2();
        return ajax_return(Code::SUCCESS,'success','',
            ['category_tree'=>get_tree($result,0,'children'),'category'=>$result]);
    }

    /**
     * 添加
     * @param Request $request
     * @return JsonResponse
     */
    public function add(Request $request)
    {
        if ($request->isMethod('get')){
            return $this->$this->ajax_return(Code::METHOD_ERROR,'error');
        }
        $category = $this->getCategory();
        return $this->ajax_return(Code::SUCCESS,'success',$category);
    }

    /**
     * 修改
     * @param Request $request
     * @return JsonResponse
     */
    public function edit(Request $request)
    {
        if ($request->isMethod('get')){
            return $this->$this->ajax_return(Code::METHOD_ERROR,'error');
        }
        $result = $this->apiListsModel->getResult('id',$this->post['id']);
        if (empty($result)){
            return $this->ajax_return(Code::ERROR,'error');
        }
        $result->response = json_decode($result->response,true);
        $result->request = json_decode($result->request,true);
        $item['apilists'] = $result;
        $item['category'] = $this->getCategory();
        return $this->ajax_return(Code::SUCCESS,'success',$item);
    }

    /**
     * 删除api
     * @param Request $request
     * @return JsonResponse
     */
    public function delete(Request $request)
    {
        if ($request->isMethod('get')){
            return $this->ajax_return(Code::METHOD_ERROR,'error');
        }
        $apiLists = $this->apiListsModel->getResult('id',$this->post['id'],'=',['role_id']);
        if ($apiLists->role_id === 1){
            return $this->ajax_return(Code::ERROR,'Permission denied');
        }
        $result = $this->apiListsModel->deleteResult('id',$this->post['id']);
        if (!empty($result)){
            $this->apiLogModel->deleteResult('api_id',$this->post['id']);
            return $this->ajax_return(Code::SUCCESS,'success');
        }
        return $this->ajax_return(Code::ERROR,'error');
    }

    /**
     * 保存数据
     * @param Request $request
     * @return JsonResponse
     */
    public function save(Request $request)
    {
        if ($request->isMethod('get')){
            return $this->ajax_return(Code::METHOD_ERROR,'error');
        }
        $validate = Validator::make($this->post,$this->rules($this->post['act']));
        if ($validate->fails()){
            return $this->ajax_return(Code::ERROR,$validate->errors()->first());
        }
        $this->post['role_id'] = '3';
        switch ($this->post['act']){
            case 'category':
                unset($this->post['act']);
                $result = $this->apiCategoryModel->addResult($this->post);
                if ($result){
                   return $this->ajax_return(Code::SUCCESS,'success');
                }
                return $this->ajax_return(Code::ERROR,'error');
                break;
            case 'lists':
                unset($this->post['act']);
                $result = $this->apiListsModel->addResult($this->post);
                if ($result){
                    $log = array(
                        'username' =>'vuedemo',
                        'desc' =>'添加接口'.$this->post['name'],
                        'updated_at' =>date("Y-m-d H:i:s"),
                        'api_id' =>$result,
                    );
                    $this->apiLogModel->addResult($log);
                    return $this->ajax_return(Code::SUCCESS,'success');
                }
                return $this->ajax_return(Code::ERROR,'error');
                break;
                break;
            default:
                return $this->ajax_return(Code::ERROR,'error');
                break;
        }
    }

    /**
     * 更新数据
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request)
    {
        if ($request->isMethod('get')){
            return $this->ajax_return(Code::METHOD_ERROR,'error');
        }
        $validate = Validator::make($this->post,$this->rules($this->post['act']));
        if ($validate->fails()){
            return $this->ajax_return(Code::ERROR,$validate->errors()->first());
        }
        $this->post['role_id'] = '3';
        switch ($this->post['act']){
            case 'category':
                unset($this->post['act']);
                unset($this->post['show_name']);
                $result = $this->apiCategoryModel->updateResult($this->post,'id',$this->post['id']);
                if ($result){
                    return $this->ajax_return(Code::SUCCESS,'success');
                }
                return $this->ajax_return(Code::ERROR,'error');
                break;
            case 'lists':
                unset($this->post['act']);
                unset($this->post['type_name']);
                $result = $this->apiListsModel->updateResult($this->post,'id',$this->post['id']);
                if ($result){
                    $log = array(
                        'username' =>'vuedemo',
                        'desc' =>'修改接口'.$this->post['name'],
                        'updated_at' =>date("Y-m-d H:i:s"),
                        'api_id' =>$this->post['id'],
                    );
                    $this->apiLogModel->addResult($log);
                    return $this->ajax_return(Code::SUCCESS,'success');
                }
                return $this->ajax_return(Code::ERROR,'error');
                break;
            default:
                return $this->ajax_return(Code::ERROR,'error');
                break;
        }
    }
    /*********************************************************************api 分类****************************************************************/
    /**
     * 删除api分类
     * @param Request $request
     * @return JsonResponse
     */
    public function CategoryDelete(Request $request)
    {
        if ($request->isMethod('get')){
            return $this->ajax_return(Code::METHOD_ERROR,'error');
        }
        $validate = Validator::make($this->post,['id'=>'required|integer']);
        if ($validate->fails()) {
            return $this->ajax_return(Code::ERROR, $validate->errors()->first());
        }
        $category = $this->apiCategoryModel->getResult('id',$this->post['id'],'=',['role_id']);
        if ($category->role_id === 1){
            return $this->ajax_return(Code::ERROR,'Permission denied');
        }
        $result = $this->apiCategoryModel->deleteResult('id',$this->post['id']);
        if (!empty($result)){
            return $this->ajax_return(Code::SUCCESS,'success');
        }
        return $this->ajax_return(Code::ERROR,'error');
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
                    'name' =>'required',
                    'desc' =>'required',
                    'method' =>'required|in:POST,GET,DELETE,PUT',
                    'href' =>'required|url',
                    'response_string' =>'required|json',
                    'type' =>'required|integer',
                    'act'=>'required|in:category,lists'
                ];
                break;
            case 'category':
                $rules = [
                    'name' =>'required',
                    'desc' =>'required',
                    'act'=>'required|in:category,lists'
                ];
                break;
        }
        return $rules;
    }
}

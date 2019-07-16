<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;
use App\Models\Article;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * 文章管理
 * Class ArticleController
 * @package App\Http\Controllers\Api\v1
 */
class ArticleController extends BaseController
{
    /**
     * 文章列表
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        if ($request->isMethod('get')){
            return $this->ajax_return(Code::METHOD_ERROR,'error');
        }
        $result = Article::getInstance()->getLists($this->post['page'],$this->post['limit'],'');
        return $this->ajax_return(Code::SUCCESS,'success',$result);
    }
    /**
     * 修改文章
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request)
    {
        if ($request->isMethod('get')){
            return $this->ajax_return(Code::METHOD_ERROR,'error');
        }
        if (!empty($this->post['act'])){
            unset($this->post['act']);
            $validate = Validator::make($this->post,['id'=> 'required|integer', 'status'=>'required|in:1,2']);
        }else{
            unset($this->post['file']);
            $validate = Validator::make($this->post,['title'=> 'required','url' =>'required','content'=>'required']);
        }
        if ($validate->fails()){
            return ajax_return(Code::ERROR,$validate->errors()->first());
        }
        $result = Article::getInstance()->updateResult($this->post,'id',$this->post['id']);
        if (!empty($result)){
            return $this->ajax_return(Code::SUCCESS,'success');
        }
        return $this->ajax_return(Code::ERROR,'error');
    }
}

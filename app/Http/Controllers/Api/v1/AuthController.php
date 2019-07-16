<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;


use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\View;

class AuthController extends BaseController
{

    /**
     * 权限列表
     * @param Request $request
     * @return Factory|JsonResponse|\Illuminate\View\View
     */
    public function index(Request $request)
    {
        if ($request->isMethod('get')){
            return view('admin.auth.index');
        }
        $result['authLists'] = $this->ruleModel->getAuthLists($this->post['name']??'',$this->post['pid']??0,$this->post['page']??1,$this->post['limit']??20);
        $result['selectAuth'] = $this->ruleModel->getResult2('1',2);
        return $this->ajax_return(Code::SUCCESS,'success','',$result);
    }

    /**
     * 权限保存
     * @param Request $request
     * @return JsonResponse
     */
    public function save(Request $request)
    {
        if ($request->isMethod('get')){
            return $this->ajax_return(Code::METHOD_ERROR,'error');
        }
        $validate = Validator::make($this->post,['name'=> 'required|unique:os_auth','href' =>'required']);
        if ($validate->fails()){
            return $this->ajax_return(Code::ERROR,$validate->errors()->first());
        }
        $result = $this->ruleModel->addResult($this->post);
        if (!empty($result)){
            $this->sys_log('权限保存成功');
            return $this->ajax_return(Code::SUCCESS,'success',route('auth-index'));
        }
        return $this->ajax_return(Code::ERROR,'error');
    }

    /**
     * 更新权限
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request)
    {
        if ($request->isMethod('get')){
            return $this->ajax_return(Code::METHOD_ERROR,'error');
        }
        if (empty($this->post['act'])){
            $validate = Validator::make($this->post,['name'=> 'required','href' =>'required']);
            if ($validate->fails()){
                return $this->ajax_return(Code::ERROR,$validate->errors()->first());
            }
            unset($this->post['show_name']);
        }else{
            $validate = Validator::make($this->post,['status'=> 'required|in:1,2','id' =>'required|integer']);
            if ($validate->fails()){
                return $this->ajax_return(Code::ERROR,$validate->errors()->first());
            }
            unset($this->post['act']);
        }
        $result = $this->ruleModel->updateResult($this->post,'id',$this->post['id']);
        if (!empty($result)){
            $this->sys_log('更新权限成功');
            return $this->ajax_return(Code::SUCCESS,'success',route('auth-index'));
        }
        return $this->ajax_return(Code::ERROR,'error');
    }

    /**
     * 删除权限
     * @param Request $request
     * @return JsonResponse
     */
    public function delete(Request $request)
    {
        if ($request->isMethod('get')){
            return $this->ajax_return(Code::METHOD_ERROR,'error');
        }
        $validate = Validator::make($this->post,['id'=>'required|integer','level'=>'gt:0']);
        if ($validate->fails()){
            return $this->ajax_return(Code::ERROR,$validate->errors()->first());
        }
        //查看下面是否还有下级权限
        $_child = $this->ruleModel->getResult('pid',$this->post['id']);
        if (!empty($_child)){
            return $this->ajax_return(Code::ERROR,'权限下面还存在下级，不能删除！');
        }
        $result = $this->ruleModel->deleteResult('id',$this->post['id']);
        if (!empty($result)){
            $this->sys_log('删除权限');
            return $this->ajax_return(Code::SUCCESS,'success');
        }
        return $this->ajax_return(Code::ERROR,'error');
    }
}

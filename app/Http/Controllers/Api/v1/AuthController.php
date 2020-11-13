<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;
use Illuminate\Http\JsonResponse;

/**
 * Class AuthController
 * @author <fl140125@gmail.com>
 * @package App\Http\Controllers\Api\v1
 */
class AuthController extends BaseController
{
    /**
     * TODO: 权限列表
     * @return JsonResponse
     */
    public function index()
    {
        $result['authLists'] = $this->authModel->getAuthLists(['*'],$this->post['id']);
        foreach ($result['authLists'] as &$item){
            $item->hasChildren = false;
            if ($this->authModel->getResult('pid',$item->id)) {
                $item->hasChildren = true;
            }
        }
        $result['selectAuth'] = $this->authModel->getResultListsByStatusAndLevel('0',2);
        return $this->ajax_return(Code::SUCCESS,'successfully',$result);
    }

    /**
     * TODO: 权限保存
     * @param string name 权限名称
     * @param string href 权限地址
     * @return JsonResponse
     */
    public function save()
    {
        $this->validatePost(['name'=> 'required|unique:os_rule','href' =>'required|unique:os_rule']);
        $result = $this->authModel->addResult($this->post);
        return !empty($result) ?  $this->ajax_return(Code::SUCCESS,'save rule successfully') : $this->ajax_return(Code::ERROR,'failed');
    }

    /**
     * TODO: 更新权限
     * @param integer id ID
     * @param string name 权限名称
     * @param string href 权限地址
     * @return JsonResponse
     */
    public function update()
    {
        if (empty($this->post['act'])){
            $this->validatePost(['name'=> 'required|string','href' =>'required|string']);
            $result = $this->authModel->updateResult($this->post,'id',$this->post['pid']);
            return !empty($result) ? $this->ajax_return(Code::SUCCESS,'update rule successfully') : $this->ajax_return(Code::ERROR,'update rule failed');
        }
        unset($this->post['act']);
        $this->validatePost(['status'=> 'required|in:1,2','id' =>'required|integer']);
        $result = $this->authModel->updateResult($this->post,'id',$this->post['id']);
        return !empty($result) ? $this->ajax_return(Code::SUCCESS,'update rule status successfully') : $this->ajax_return(Code::ERROR,'update rule status failed');
    }

    /**
     * TODO: 删除权限
     * @param integer id
     * @param integer level 权限层级
     * @return JsonResponse
     */
    public function delete()
    {
        $this->validatePost(['id'=>'required|integer','level'=>'gt:0']);
        //查看下面是否还有下级权限
        $_child = $this->authModel->getResult('pid',$this->post['id']);
        if (!empty($_child)){
            return $this->ajax_return(Code::ERROR,'Cannot be delete');
        }
        $result = $this->authModel->deleteResult('id',$this->post['id']);
        return !empty($result) ? $this->ajax_return(Code::SUCCESS,'remove rule successfully') : $this->ajax_return(Code::ERROR,'remove rule failed');
    }
}

<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

/**
 * Class AuthController
 * @author <fl140125@gmail.com>
 * @package App\Http\Controllers\Api\v1
 */
class AuthController extends BaseController
{
    /**
     * todo 权限列表
     * @return JsonResponse
     */
    public function index()
    {
        $result['authLists'] = $this->ruleModel->getAuthLists($this->post['name']??'',$this->post['pid']??0,$this->post['page']??1,$this->post['limit']??15);
        $result['selectAuth'] = $this->ruleModel->getResult2('1',2);
        return $this->ajax_return(Code::SUCCESS,'successfully',$result);
    }

    /**
     * todo 权限保存
     * @return JsonResponse
     */
    public function save()
    {
        $this->validatePost(['name'=> 'required|unique:os_rule','href' =>'required|unique:os_rule']);
        $result = $this->ruleModel->addResult($this->post);
        if (!empty($result)){
            return $this->ajax_return(Code::SUCCESS,'save rule successfully');
        }
        return $this->ajax_return(Code::ERROR,'error');
    }

    /**
     * todo 更新权限
     * @return JsonResponse
     */
    public function update()
    {
        if (empty($this->post['act'])){
            $this->validatePost(['name'=> 'required|string','href' =>'required|string']);
            $result = $this->ruleModel->updateResult($this->post,'id',$this->post['pid']);
            if (!empty($result)){
                return $this->ajax_return(Code::SUCCESS,'update rule successfully');
            }
            return $this->ajax_return(Code::ERROR,'update rule error');
        }
        unset($this->post['act']);
        $this->validatePost(['status'=> 'required|in:1,2','id' =>'required|integer']);
        $result = $this->ruleModel->updateResult($this->post,'id',$this->post['id']);
        if (!empty($result)){
            return $this->ajax_return(Code::SUCCESS,'update rule status successfully');
        }
        return $this->ajax_return(Code::ERROR,'update rule status error');
    }

    /**
     * todo 删除权限
     * @return JsonResponse
     */
    public function delete()
    {
        $this->validatePost(['id'=>'required|integer','level'=>'gt:0']);
        //查看下面是否还有下级权限
        $_child = $this->ruleModel->getResult('pid',$this->post['id']);
        if (!empty($_child)){
            return $this->ajax_return(Code::ERROR,'权限下面还存在下级，不能删除！');
        }
        $result = $this->ruleModel->deleteResult('id',$this->post['id']);
        if (!empty($result)){
            return $this->ajax_return(Code::SUCCESS,'successfully');
        }
        return $this->ajax_return(Code::ERROR,'error');
    }
}

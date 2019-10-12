<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;
use App\Mail\Oauth;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;

/**
 * TODO：授权列表
 * Class OauthController
 * @author <fl140125@gmail.com>
 * @package App\Http\Controllers\Api\v1
 */
class OauthController extends BaseController
{
    /**
     * TODO：授权列表
     * @return JsonResponse
     */
    public function index()
    {
        $this->validatePost(['page'=>'required|integer|gt:0','limit'=>'required|integer']);
        $result = $this->oauthModel->getResultLists($this->post['page'],$this->post['limit'],$this->users);
        $oauthImageLists = [];
        foreach ($result['data'] as &$item){
            $item->created_at = date('Y-m-d H:i:s',$item->created_at);
            $item->updated_at = date('Y-m-d H:i:s',$item->updated_at);
            $item->email = empty($item->email) ? '' :$item->email;
            $item->code = empty($item->code) ? '' :$item->code;
            $item->oauth_type = strtoupper($item->oauth_type);
            array_push($oauthImageLists,$item->avatar_url);
        }
        $result['avatar_url_lists'] = $oauthImageLists;
        $result['roleLists'] = $this->roleModel->getResult2('1',['id','role_name']);
        return $this->ajax_return(Code::SUCCESS,'successfully',$result);
    }

    /**
     * TODO：更新授权用户
     * @return JsonResponse
     */
    public function update()
    {
        if (!empty($this->post['act'])) {
            $this->validatePost(['id'=>'required|integer','status'=>'required|integer|in:1,2']);
            unset($this->post['act']);
        } else {
            $this->validatePost(['username'=>'required|string','avatar_url'=>'required|url','role_id'=>'required|integer','status'=>'required|integer|in:1,2']);
            $this->post['created_at'] = strtotime($this->post['created_at']);
            $this->post['oauth_type'] = strtolower($this->post['oauth_type']);
        }
        $result = $this->oauthModel->updateResult($this->post,'id',$this->post['id']);
        if ($result){
            return $this->ajax_return(Code::SUCCESS,'update oauth successfully');
        }
        return $this->ajax_return(Code::ERROR,'update oauth failed');
    }

    /**
     * TODO:验证邮箱是否正确
     * @return JsonResponse
     */
    public function email()
    {
        $this->validatePost(['email'=>'required|string|email|unique:os_oauth', 'id=>required|integer', 'username'=>'required|string', 'remember_token'=>'required|string']);
        $this->post['verify_code'] = get_round_num(6,'number');
        try{
            Mail::to($this->post['email'])->send(new Oauth($this->post));
            if (!Mail::failures()) {
                $data = array(
                    'email' => $this->post['email'],
                    'code'  => $this->post['verify_code']
                );
                $result = $this->oauthModel->updateResult($data,'id',$this->post['id']);
                if ($result){
                    return $this->ajax_return(Code::SUCCESS,'email send successfully');
                }
                return $this->ajax_return(Code::ERROR,'email send failed');
            }
            return $this->ajax_return(Code::ERROR,'please enter the correct email address');
        }catch (\Exception $exception){
            return $this->ajax_return(Code::ERROR,'please enter the correct email address');
        }
    }

    /**
     * TODO:校验验证码是否正确
     * @return JsonResponse
     */
    public function code()
    {
        $this->validatePost(['code'=>'required|string','id=>required|integer']);
        $result = $this->oauthModel->getResult('id',$this->post['id']);
        if ($result->code === $this->post['code']) {
            return $this->ajax_return(Code::SUCCESS,'verify code successfully');
        }
        return $this->ajax_return(Code::SUCCESS,'verify code successfully');
    }

    /**
     * TODO：删除授权用户
     * @return JsonResponse
     */
    public function delete()
    {
        $this->validatePost(['id'=>'required|integer']);
        $result = $this->oauthModel->deleteResult('id',$this->post['id']);
        if ($result){
            return $this->ajax_return(Code::SUCCESS,'remove oauth successfully');
        }
        return $this->ajax_return(Code::ERROR,'remove oauth failed');
    }
}

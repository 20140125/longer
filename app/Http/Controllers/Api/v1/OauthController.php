<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;
use Illuminate\Http\JsonResponse;
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
        $result = $this->oauthModel->getResultLists($this->post['page'],$this->post['limit'],$this->users->username);
        $oauthImageLists = [];
        foreach ($result['data'] as &$item){
            $item->created_at = date('Y-m-d H:i:s',$item->created_at);
            $item->updated_at = date('Y-m-d H:i:s',$item->updated_at);
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

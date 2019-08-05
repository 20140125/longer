<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

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
        $validate = Validator::make($this->post,['page'=>'required|integer|gt:0','limit'=>'required|integer']);
        if ($validate->fails()){
            return $this->ajax_return(Code::ERROR,$validate->errors()->first());
        }
        $result = $this->oauthModel->getResultLists($this->post['page']??1,$this->post['limit']??15);
        $oauthImageLists = [];
        foreach ($result['data'] as &$item){
            $item->created_at = date('Y-m-d H:i:s',$item->created_at);
            $item->updated_at = date('Y-m-d H:i:s',$item->updated_at);
            $item->oauth_type = strtoupper($item->oauth_type);
            array_push($oauthImageLists,$item->avatar_url);
        }
        $result['avatar_url_lists'] = $oauthImageLists;
        return $this->ajax_return(Code::SUCCESS,'successfully',$result);
    }
    public function save()
    {
        return $this->ajax_return(Code::SUCCESS,'successfully');
    }
    public function update()
    {
        return $this->ajax_return(Code::SUCCESS,'successfully');
    }
    public function delete()
    {
        return $this->ajax_return(Code::SUCCESS,'successfully');
    }
}

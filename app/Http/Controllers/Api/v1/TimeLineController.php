<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;
use App\Models\TimeLine;
use Illuminate\Http\JsonResponse;

/**
 * Class TimeLineController
 * @author <fl140125@gmail.com>
 * @package App\Http\Controllers\Api\v1
 */
class TimeLineController extends BaseController
{
    /**
     * TODO:时间线列表
     * @return JsonResponse
     */
    public function index()
    {
        $this->validatePost(['page'=>'required|integer|gt:0','limit'=>'required|integer|gt:0']);
        $result = TimeLine::getInstance()->getResultLists($this->post['page'] ?? 1,$this->post['limit'] ?? 10);
        return $this->ajax_return(Code::SUCCESS,'successfully',$result);
    }

    /**
     * TODO:保存
     * @return JsonResponse
     */
    public function save()
    {
        $this->validatePost(['content'=>'required|string','timestamp'=>'required|string','type'=>'required|string|in:primary,success,warning,danger,info']);
        $result = TimeLine::getInstance()->saveResult($this->post);
        if (!empty($result)) {
            return $this->ajax_return(Code::SUCCESS,'timeline save successfully');
        }
        return $this->ajax_return(Code::ERROR,'timeline save failed');

    }

    /**
     * TODO:更新
     * @return JsonResponse
     */
    public function update()
    {
        $this->validatePost(['id'=>'required|integer','content'=>'required|string','timestamp'=>'required|string','type'=>'required|string|in:primary,success,warning,danger,info']);
        $result = TimeLine::getInstance()->updateResult($this->post,'id',$this->post['id']);
        if (!empty($result)) {
            return $this->ajax_return(Code::SUCCESS,'timeline update successfully');
        }
        return $this->ajax_return(Code::ERROR,'timeline update failed');
    }
}

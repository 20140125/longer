<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;
use App\Models\Chat;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class ChatController
 * @author <fl140125@gmail.com>
 * @package App\Http\Controllers\Api\v1
 */
class ChatController extends BaseController
{
    /**
     * @var Chat $chatModel
     */
    protected  $chatModel;

    /**
     * ChatController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->chatModel = Chat::getInstance();
    }

    /**
     * TODO:获取历史聊天记录
     * @return JsonResponse
     */
    public function history()
    {
        $this->validatePost(['from_client_name'=>'required|string','to_client_name'=>'string|required']);
        $result['data'] = $this->chatModel->getResultLists($this->post['from_client_name'],$this->post['to_client_name'],$this->post['page']??1,$this->post['limit']??20);
        $result['pages'] =  $this->chatModel->getTotalPages($this->post['from_client_name'],$this->post['to_client_name'],$this->post['limit']??20);
        return $this->ajax_return(Code::SUCCESS,'successfully',$result);
    }
}

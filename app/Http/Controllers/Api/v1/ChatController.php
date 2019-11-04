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
    public function index()
    {
        $this->validatePost(['page'=>'required|integer','limit'=>'integer|required']);
        $where = [];
        if (!empty($this->post['from'])) {
            $where[] = ['from_client_name',$this->post['from']];
        }
        if (!empty($this->post['to'])) {
            $where[] = ['to_client_name',$this->post['to']];
        }
        $result = $this->chatModel->getResult($where,$this->post['limit'],$this->post['page']);
        return $this->ajax_return(Code::SUCCESS,'successfully',$result);
    }
}

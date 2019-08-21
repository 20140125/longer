<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;
use App\Models\Push;
use Illuminate\Http\Request;

/**
 * Class PushController
 * @author <fl140125@foxmail.com>
 * @package App\Http\Controllers\Api\v1
 */
class PushController extends BaseController
{
    /**
     * @var Push $pushModel
     */
    protected $pushModel;

    /**
     * PushController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->pushModel = Push::getInstance();
    }

    /**
     * TODO:推送列表
     * @return mixed
     */
    public function index()
    {
        $this->validatePost(['page'=>'required|integer|gt:0','limit'=>'required|integer|gt:0']);
        $result = $this->pushModel->getResultLists($this->post['page'],$this->post['limit'],$this->post['state'],$this->post['status']);
        return $this->ajax_return(Code::SUCCESS,'successfully',$result);
    }
}

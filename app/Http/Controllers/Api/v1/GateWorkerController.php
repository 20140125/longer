<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;
use Exception;
use GatewayWorker\Lib\Gateway;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class GateWorkerController extends BaseController
{
    public function __construct(Request $request)
    {
        parent::__construct($request);
        Gateway::$registerAddress='127.0.0.1:1236';
    }

    /**
     *
     */
    public function bindUid()
    {
        $this->validatePost(['uid'=>'required|string','client_id'=>'required']);
        try {
            return Gateway::sendToAll($this->post['uid'] . '绑定成功');
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }
}

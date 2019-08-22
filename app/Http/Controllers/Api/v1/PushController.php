<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;
use App\Http\Controllers\Utils\RedisClient;
use App\Models\Push;
use Exception;
use Illuminate\Http\JsonResponse;
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
     * @var RedisClient $redisClient
     */
    protected $redisClient;

    /**
     * PushController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->pushModel = Push::getInstance();
        $this->redisClient = new RedisClient('127.0.0.1');
    }

    /**
     * TODO:推送列表
     * @return JsonResponse
     */
    public function index()
    {
        $this->validatePost(['page'=>'required|integer|gt:0','limit'=>'required|integer|gt:0']);
        $result = $this->pushModel->getResultLists($this->post['page'],$this->post['limit'],$this->post['state'],$this->post['status']);
        foreach ($result['data'] as &$item) {
            $item->created_at = date('Y-m-d H:i:s',$item->created_at);
        }
        $result['oauth'] = $this->oauthModel->getOauthLists([],['username']);
        return $this->ajax_return(Code::SUCCESS,'successfully',$result);
    }

    /**
     *  TODO：添加站内推送信息
     * @return JsonResponse
     */
    public function save()
    {
        $this->validatePost(['info'=>'required|string','username'=>'required|string','status'=>'required|integer|in:1,2','created_at'=>'required|string|date']);
        $this->pushMessage();
        $result = $this->pushModel->addResult($this->post);
        if ($result && $this->post['state'] === Code::WebSocketState[0]) {
            return $this->ajax_return(Code::SUCCESS,'push message '.$this->post['state']);
        }
        if ($this->post['state'] === Code::WebSocketState[2]) {
            return $this->ajax_return(Code::SUCCESS,$this->post['username'].' already '.$this->post['state']);
        }
        return $this->ajax_return(Code::ERROR,'push message '.$this->post['state']);
    }

    /**
     * TODO：推送站内未执行的推送的消息
     * @return JsonResponse
     */
    public function update()
    {
        $this->validatePost(['id'=>'required|integer','info'=>'required|string','username'=>'required|string','status'=>'required|integer|in:1,2','created_at'=>'required|string|date']);
        $this->pushMessage();
        $result = $this->pushModel->updateResult($this->post,'id',$this->post['id']);
        if ($result && $this->post['state'] === Code::WebSocketState[0]) {
            return $this->ajax_return(Code::SUCCESS,'push message '.$this->post['state']);
        }
        if ($this->post['state'] === Code::WebSocketState[2]) {
            return $this->ajax_return(Code::SUCCESS,$this->post['username'].' already '.$this->post['state']);
        }
        return $this->ajax_return(Code::ERROR,'push message '.$this->post['state']);
    }

    /**
     * TODO：推送站内信息处理
     */
    private function pushMessage()
    {
        $this->post['state'] = Code::WebSocketState[1];
        $this->post['created_at'] = strtotime($this->post['created_at']);
        $this->post['uid'] = $this->post['username'] == 'all' ? 'none' : $this->post['uid'];
        if ($this->post['status'] == '1') {
            try{
                //推送给所有人
                if ($this->post['username'] == 'all') {
                    if ($this->workerManPush($this->post['info'])) {
                        $this->post['state'] = Code::WebSocketState[0];
                        return ;
                    }
                    $this->post['state'] = Code::WebSocketState[2];
                }
                //推送给个人
                if ($this->redisClient->sIsMember('uidConnectionMap',$this->post['uid'])) {
                    if ($this->workerManPush($this->post['info'],$this->post['uid'])) {
                        $this->post['state'] = Code::WebSocketState[0];
                        return ;
                    }
                    $this->post['state'] = Code::WebSocketState[2];
                    return ;
                }
                $this->post['state'] = Code::WebSocketState[2];
            }catch (Exception $e){
                act_log('站内信息推送失败：'.$e->getMessage());
            }
        }
    }

    /**
     * TODO：删除记录
     * @return JsonResponse
     */
    public function delete()
    {
        $this->validatePost(['id'=>'required|integer']);
        $result = $this->pushModel->deleteResult('id',$this->post['id']);
        if (!empty($result)) {
            return $this->ajax_return(Code::SUCCESS,'remove push successfully');
        }
        return $this->ajax_return(Code::SUCCESS,'remove push failed');
    }
}

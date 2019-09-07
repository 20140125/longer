<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;
use App\Http\Controllers\Utils\RedisClient;
use App\Jobs\OauthProcess;
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
     * @return JsonResponse
     */
    public function index()
    {
        $this->validatePost(['page'=>'required|integer|gt:0','limit'=>'required|integer|gt:0']);
        $result = $this->pushModel->getResultLists($this->post['page'],$this->post['limit'],$this->users,$this->post['state'],$this->post['status']);
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
        $this->post['created_at'] = time();
        if ($this->post['username'] == 'all') {
            dispatch(new OauthProcess($this->post))->onQueue('push')->delay(30);
            $this->post['username'] = 'admin';
            $this->post['uid'] = md5('admin');
            $this->pushModel->addResult($this->post);
            return $this->ajax_return(Code::SUCCESS,'push message save successfully');
        }
        $this->pushModel->addResult($this->post);
        return $this->ajax_return(Code::SUCCESS,'push message '.$this->post['state']);
    }

    /**
     * TODO：推送站内未执行的推送的消息
     * @return JsonResponse
     */
    public function update()
    {
        $this->validatePost(['id'=>'required|integer','info'=>'required|string','username'=>'required|string','status'=>'required|integer|in:1,2','created_at'=>'required|string|date']);
        $this->pushMessage();
        $this->post['created_at'] = strtotime($this->post['created_at']);
        if ($this->post['username'] == 'all') {
            dispatch(new OauthProcess($this->post))->onQueue('push')->delay(30);
            return $this->ajax_return(Code::SUCCESS,'push message update successfully');
        }
        $this->pushModel->updateResult($this->post,'id',$this->post['id']);
        return $this->ajax_return(Code::SUCCESS,'push message '.$this->post['state']);
    }
    /**
     * TODO：查看站内推送的消息
     * @return JsonResponse
     */
    public function read()
    {
        $this->validatePost(['id'=>'required|integer','see'=>'required|integer']);
        $params['state'] = 'successfully';
        $params['id'] = $this->post['id'];
        $params['see'] = $this->post['see'];
        $this->pushModel->updateResult($params,'id',$this->post['id']);
        return $this->ajax_return(Code::SUCCESS,'successfully');
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

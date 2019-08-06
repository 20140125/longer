<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;
use App\Models\Config;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Class ConfigController
 * @author <fl140125@gmail.com>
 * @package App\Http\Controllers\Api\v1
 */
class ConfigController extends BaseController
{
    /**
     * @var Config $configModel
     */
    protected $configModel;

    /**
     * ConfigController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->configModel = Config::getInstance();
    }
    /**
     * TODO：获取配置列表
     * @return JsonResponse
     */
    public function index()
    {
        $result = $this->configModel->getResultLists();
        foreach ($result as &$item){
            $item->created_at = date('Y-m-d H:i:s',$item->created_at);
            $item->updated_at = date('Y-m-d H:i:s',$item->updated_at);
            if (!empty($item->value)){
                $item->children = json_decode($item->value,true);
            }
        }
        return $this->ajax_return(Code::SUCCESS,'successfully',$result);
    }

    /**
     * TODO：保存配置
     * @return JsonResponse
     */
    public function save()
    {
        $this->validatePost(['name'=>'required|string|unique:os_config']);
        $result = $this->configModel->addResult($this->post);
        if ($result){
            return $this->ajax_return(Code::SUCCESS,'save config successfully');
        }
        return $this->ajax_return(Code::ERROR,'save config failed');
    }
    /**
     * TODO：更新配置
     * @return JsonResponse
     */
    public function update()
    {
        if (!empty($this->post['act']) && $this->post['act'] === 'status'){
            $rule = ['id'=>'required|integer','status'=>'required|integer|in:1,2'];
        } else {
            $rule = ['id'=>'required|integer','name'=>'required|string'];
        }
        $this->validatePost($rule);
        $result = $this->configModel->updateResult($this->post,'id',$this->post['id']);
        if ($result){
            return $this->ajax_return(Code::SUCCESS,'update config successfully');
        }
        return $this->ajax_return(Code::ERROR,'update config failed');
    }

    /**
     * TODO：更新配置值
     * @return JsonResponse
     */
    public function value()
    {
        $this->validatePost(['id'=>'required|integer','name'=>'required|string']);
        $result = $this->configModel->updateValResult($this->post,'id',$this->post['pid']);
        if ($result){
            return $this->ajax_return(Code::SUCCESS,'update config value successfully');
        }
        return $this->ajax_return(Code::ERROR,'update config value failed');
    }

    /**
     * TODO：删除配置
     * @return JsonResponse
     */
    public function delete()
    {
        $this->validatePost(['id'=>'required|integer']);
        $result = $this->configModel->deleteResult($this->post,'id',$this->post['id']);
        if ($result){
            return $this->ajax_return(Code::SUCCESS,'remove config successfully');
        }
        return $this->ajax_return(Code::ERROR,'remove config failed');
    }
}

<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;
use App\Mail\Notice;
use App\Models\Push;
use App\Models\ReqRule;
use App\Models\Users;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * TODO：用户请求授权
 * Class ReqRuleController
 * @author <fl140125@gmail.com>
 * @package App\Http\Controllers\Api\v1
 */
class ReqRuleController extends BaseController
{
    /**
     * @var ReqRule $reqRuleModel
     */
    protected $reqRuleModel;
    /**
     * ReqRuleController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->reqRuleModel = ReqRule::getInstance();
    }

    /**
     * TODO：用户请求权限列表
     * @param integer page
     * @param integer limit
     * @return JsonResponse
     */
    public function index()
    {
        $this->validatePost(['page'=>'required|integer','limit'=>'required|integer']);
        $result = $this->reqRuleModel->getResultLists($this->post['page'],$this->post['limit'],$this->users);
        foreach ($result['data'] as &$item) {
            $item->created_at = empty($item->created_at) ? '' : date('Y-m-d H:i:s',$item->created_at);
            $item->updated_at = empty($item->updated_at) ? '' : date('Y-m-d H:i:s',$item->updated_at);
            $item->expires = empty($item->expires) ? '' : date('Y-m-d H:i:s',$item->expires);
            $item->ruleLists = $this->setAuth($item->user_id);
        }
        $result['userLists'] = $this->userModel->getAll([],['id','username']);
        return $this->ajax_return(Code::SUCCESS,'successfully',$result);
    }

    /**
     * TODO：根据角色获取权限
     * @param integer user_id
     * @return JsonResponse
     */
    public function getAuth()
    {
        $this->validatePost(['user_id'=>'required|integer']);
        $ruleLists = $this->setAuth($this->post['user_id']);
        return $this->ajax_return(Code::SUCCESS,'successfully',$ruleLists);
    }

    /**
     * TODO：获取权限
     * @param $userId
     * @return Collection
     */
    private function setAuth($userId)
    {
        //获取所有权限
        $ruleLists = $this->authModel->getResultListsByStatusAndLevel('0',0);
        //获取待授权角色
        $where[] = ['id',$userId];
        $users = $this->userModel->getResult($where);
        //角色权限
        $role = $this->roleModel->getResult('id',$users->role_id);
        $loginAuth = json_decode($role->auth_url,true);
        foreach ($ruleLists as &$item){
            $item->disable = false;
            if (in_array($item->href,$loginAuth)) {
                $item->disable = true;
            }
        }
        return $ruleLists;
    }

    /**
     * TODO：保存用户请求权限
     * @param string username
     * @param array href
     * @return JsonResponse
     */
    public function save()
    {
        $this->validatePost(['user_id'=>'required|string','href'=>'required|Array',
            'expires' => 'required|date|before:'.date('Y-m-d H:i:s',strtotime('+1 year')).'|after:'.date('Y-m-d H:i:s')],
        [
            'expires.before' => 'authorization expires must be a date before '.date('Y-m-d H:i:s',strtotime('+1 year')),
            'expires.after'  => 'authorization expires must be a date after '.date('Y-m-d H:i:s')
        ]);
        DB::beginTransaction();
        try {
            $users = $this->userModel->getResult('id',$this->post['user_id']);
            if (empty($users)) {
                return $this->ajax_return(Code::ERROR,'user does not exist');
            }
            $req['username'] = $users->username;
            $req['user_id'] = $users->id;
            $req['expires'] = $this->post['expires'] ?? strtotime($this->post['expires']);
            $req['desc'] = $this->post['desc'] ?? '申请权限授权';
            $where[] = ['user_id',$req['user_id']];
            $result = 0;
            foreach ($this->post['href'] as $href){
                $req['href'] = str_replace('v1','admin',$href);
                $where[] = ['href',$req['href']];
                $reqRes = $this->reqRuleModel->getResult($where);
                $result = !empty($reqRes) ? $this->reqRuleModel->updateResult($req,$where) : $this->reqRuleModel->addResult($req);
            }
            $message = array('username' => $users->username, 'info' => $users->username.'申请权限待审批，请周知~', 'uid'  => $users->uuid, 'title' => '申请权限', 'created_at' => time());
            $this->pushMessage();
            Push::getInstance()->addResult($message);
            DB::commit();
            return $result ? $this->ajax_return(Code::SUCCESS,'save request authorization successfully') : $this->ajax_return(Code::ERROR,'save request authorization failed');
        } catch (Exception $exception) {
            Log::error($exception->getMessage());
            DB::rollBack();
            return $this->ajax_return(Code::SERVER_ERROR,'save request authorization failed');
        }
    }

    /**
     * TODO：更新请求授权信息
     * @param string act 用户行为
     * @param integer status 状态
     * @param integer id
     * @param string username 用户名
     * @param array href 授权集合
     * @param integer expires 过期时间
     * @param string created_at 添加时间
     * @return JsonResponse
     */
    public function update()
    {
        if (empty($this->post['act'])) {
            $this->validatePost([
                'status'=>'required|integer',
                'id'=>'required|integer',
                'username'=>'required|string',
                'href'=>'required|Array',
                'created_at' => 'required|date',
                'expires' => 'required|date|before:'.date('Y-m-d H:i:s',strtotime('+1 year')).'|after:'.date('Y-m-d H:i:s'),
            ],[
                'expires.before' => 'authorization expires must be a date before '.date('Y-m-d H:i:s',strtotime('+1 year')),
                'expires.after'  => 'authorization expires must be a date after '.date('Y-m-d H:i:s')
            ]);
            $post['created_at'] = strtotime($this->post['created_at']);
            $post['expires'] = strtotime($this->post['expires']);
            $post['href'] = implode('',$this->post['href']);
            $post['desc'] = $this->post['desc'];
            $result = $this->reqRuleModel->updateResult($post,'id',$this->post['id']);
            return empty($result) ? $this->ajax_return(Code::ERROR,'update authorization status error') : $this->ajax_return(Code::SUCCESS,'update authorization status successfully') ;
        }
        $this->validatePost(['status' => 'required|integer|in:1,2', 'id' => 'required|integer|exists:os_req_rule']);
        if ($this->post['status'] == 2) {
            return $this->delete();
        }
        $getReq = $this->reqRuleModel->getResult('id', $this->post['id']);
        //用户没有申请时长暂定 7 天权限
        if (empty($getReq->expires)) {
            $this->post['expires'] = strtotime('+7 day');
            $this->post['desc'] = '系统自动生成的申请权限';
        } else {
            $this->post['expires'] = strtotime('+30 day');
            $this->post['desc'] = '重新申请过期权限';
        }
        DB::beginTransaction();
        try {
            //获取当前角色ID
            $role_id = $this->userModel->getResult('id',$getReq->user_id,'=',['role_id'])->role_id;
            //获取当前角色信息
            $role = $this->roleModel->getResult('id',$role_id,'=',['auth_ids','auth_url']);
            //根据链接获取权限ID
            $rule_id = $this->authModel->getResult('href',$getReq->href,'=',['id'])->id;
            //权限ID
            $role->auth_ids = json_decode($role->auth_ids);
            if (!in_array($rule_id, $role->auth_ids)) {
                array_push( $role->auth_ids,$rule_id);
            }
            //权限地址
            $role->auth_url = json_decode($role->auth_url);
            if (!in_array($getReq->href,$role->auth_url)) {
                array_push($role->auth_url,$getReq->href);
            }
            //更新角色记录
            $this->roleModel->updateResult(['auth_ids'=>json_encode($role->auth_ids),'auth_url'=>str_replace('\\','',json_encode($role->auth_url))],'id',$role_id);
            //修改申请权限列表
            unset($this->post['act']);
            $result = $this->reqRuleModel->updateResult($this->post,'id',$this->post['id']);
            DB::commit();
            return empty($result) ? $this->ajax_return(Code::ERROR,'update authorization error') : $this->ajax_return(Code::SUCCESS,'update authorization successfully') ;
        } catch (Exception $exception) {
            Log::error(json_encode([$exception->getMessage(),$this->post]));
            DB::rollBack();
            return $this->ajax_return(Code::SERVER_ERROR,'update authorization error');
        }
    }

    /**
     * TODO：删除申请授权记录
     * @param integer id
     * @return JsonResponse
     */
    public function delete()
    {
        $this->validatePost(['id'=>'required|integer|exists:os_req_rule']);
        //获取当前申请权限记录
        $getReq = $this->reqRuleModel->getResult('id',$this->post['id']);
        //获取当前申请授权用户信息
        $role_id = $this->userModel->getResult('id',$getReq->user_id,'=',['role_id'])->role_id;
        //获取当前用户的角色信息
        $role = $this->roleModel->getResult('id', $role_id,'=',['auth_ids']);
        $role->auth_ids = json_decode($role->auth_ids);
        //根据用户请求授权地址获取该条规则信息
        $rule_id = $this->authModel->getResult('href',$getReq->href,'=',['id'])->id;
        array_splice($role->auth_ids,find_str_in_array($role->auth_ids,$rule_id),1);
        $authLists = $this->authModel->getResult('id', $role->auth_ids,'in',['href']);
        $auth_url = array();
        foreach ($authLists as $item){
            $auth_url[] = $item->href;
        }
        DB::beginTransaction();
        try{
            $result = $this->reqRuleModel->updateResult(['status'=>2,'updated_at'=>time(),'expires'=>0,'desc'=>'权限被管理员收回'],'id',$this->post['id']);
            if ($result){
                $data = array(
                    'auth_ids' => json_encode($role->auth_ids),
                    'auth_url' => str_replace('\\','',json_encode($auth_url)),
                    'updated_at' => time()
                );
                $this->roleModel->updateResult($data,'id',$role_id);
            }
            DB::commit();
            return $result ? $this->ajax_return(Code::SUCCESS,'remove authorization successfully') : $this->ajax_return(Code::ERROR,'remove authorization failed');
        }catch (Exception $exception){
            DB::rollBack();
            Log::error($exception->getMessage());
            return $this->ajax_return(Code::SERVER_ERROR,'remove authorization failed');
        }
    }
}

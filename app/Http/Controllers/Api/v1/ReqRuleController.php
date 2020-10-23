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
            if (in_array(strtolower($item->href),$loginAuth)) {
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
        $this->validatePost(['user_id'=>'required|string','href'=>'required|Array']);
        if (isset($this->post['expires']) && !empty($this->post['expires'])){
            if (strtotime($this->post['expires'])>strtotime('+1 year')) {
                return $this->ajax_return(Code::ERROR,'authorization expires must be a date before '.date('Y-m-d H:i:s',strtotime('+1 year')));
            }
            if (strtotime($this->post['expires'])<= time()) {
                return $this->ajax_return(Code::ERROR,'authorization expires must be a date after '.date('Y-m-d H:i:s'));
            }
        }
        $users = $this->userModel->getResult('id',$this->post['user_id']);
        if (empty($users)) {
            return $this->ajax_return(Code::ERROR,'user does not exist');
        }
        $req['username'] = $users->username;
        $req['user_id'] = $users->id;
        $req['expires'] = empty($this->post['expires']) ? 0 : strtotime($this->post['expires']);
        $req['desc'] = empty($this->post['desc']) ? '申请权限授权' : $this->post['desc'];
        $where[] = ['user_id',$req['user_id']];
        $result = 0;
        foreach ($this->post['href'] as &$href){
            $href = str_replace('v1','admin',$href);
            $req['href'] = $href;
            $where[] = ['href',$href];
            $reqRes = $this->reqRuleModel->getResult($where);
            if (!empty($reqRes)) {
                $result = $this->reqRuleModel->updateResult($req,$where);
            } else {
                $result = $this->reqRuleModel->addResult($req);
            }
            if ($this->users->username != 'admin') {
                //推送站内信息
                $this->post['info'] = $req['username'].'申请权限('.config('app.url').str_replace('/admin/','api/v1/',$req['href']).')';
                $this->post['username'] = 'admin';
                $this->post['uid'] = Users::getInstance()->getResult('username',$this->post['username'],'=',['uuid'])->uuid;
                $this->post['status'] = 1;
                $this->pushMessage();
                $message = array(
                    'username' => $this->post['username'],
                    'info' => $this->post['info'],
                    'uid'  => $this->post['uid'],
                    'state' => $this->post['state'],
                    'title' => '权限申请',
                    'status' => 1,
                    'created_at' => time()
                );
                Push::getInstance()->addResult($message);
            }
        }
        if ($result) {
            return $this->ajax_return(Code::SUCCESS,'save request authorization successfully');
        }
        return $this->ajax_return(Code::SUCCESS,'save request authorization failed');
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
        if (!empty($this->post['act'])) {
            //同意用户申请授权信息
            $this->validatePost(['status'=>'required|integer|in:1,2','id'=>'required|integer|exists:os_req_rule']);
            $getReq = $this->reqRuleModel->getResult('id',$this->post['id']);
            //用户没有申请时长暂定 7 天权限
            if (empty($getReq->expires)) {
                $this->post['expires'] = strtotime('+7 day');
                $this->post['desc'] = '系统自动生成的申请权限';
            } else {
                $this->post['expires'] = strtotime('+30 day');
                $this->post['desc'] = '重新申请过期权限';
            }
            unset($this->post['act']);
            //开启事务
            DB::beginTransaction();
            $result = $this->reqRuleModel->updateResult($this->post,'id',$this->post['id']);
            if ($result) {
                //获取当前申请授权用户信息
                $users = $this->userModel->getResult('id',$getReq->user_id);
                //获取当前用户的角色信息
                $role = $this->roleModel->getResult('id', $users->role_id);
                $auth_ids = json_decode($role->auth_ids,true);
                $auth_url = json_decode($role->auth_url,true);
                //根据用户请求授权地址获取该条规则信息
                $rule = $this->authModel->getResult('href',$getReq->href);
                if (!in_array((int)$rule->id,$auth_ids)) {
                    array_push($auth_ids,(int)$rule->id);
                }
                if (!in_array($rule->href,$auth_url)) {
                   array_push($auth_url,$rule->href);
                }
                try{
                    $setReq = array(
                        'role_name' => $getReq->username,
                        'auth_ids' => json_encode($auth_ids),
                        'auth_url' => str_replace('\\','',json_encode($auth_url)),
                        'status' => 1,
                        'created_at' => time(),
                        'updated_at' => time(),
                    );
                    //判断当前申请授权用户是否存在记录
                    $existsRole = $this->roleModel->getResult('id',$users->role_id);
                    if ($existsRole) {
                        //修改角色信息
                        $this->roleModel->updateResult($setReq,'role_name',$setReq['role_name']);
                    } else {
                        //生成新的角色
                        $data['role_id'] = $this->roleModel->addResult($setReq);
                        //修改请求授权用户的角色ID
                        $this->userModel->updateResult($data,'id',$users->id);
                    }
                    Log::error(json_encode(Users::getInstance()->getResult('username',$users->username,'=',['uuid'])));
                    //推送站内信息
                    $this->post['username'] = $getReq->username;
                    $this->post['info'] = '你申请的权限已经审核通过~！';
                    $this->post['uid'] = Users::getInstance()->getResult('username',$users->username,'=',['uuid'])->uuid;
                    $this->post['status'] = 1;
                    $this->pushMessage();
                    $message = array(
                        'username' => $this->post['username'],
                        'info' => $this->post['info'],
                        'uid'  => $this->post['uid'],
                        'state' => $this->post['state'],
                        'status' => 1,
                        'title' => '权限通过',
                        'created_at' => time()
                    );
                    Push::getInstance()->addResult($message);
                    //如果用户已经绑定邮箱，并且站内通知没有成功，发送邮件告知用户授权已经成功
                    if (!empty($users->email) && $this->post['state']!=Code::WebSocketState[0]) {
                        try {
                            $mail = array( 'href' =>$getReq->href, 'rule_name' => $rule->name,'username' =>$getReq->username,'remember_token'=>$users->remember_token );
                            Mail::to($users->email)->send(new Notice($mail));
                        } catch (Exception $exception) {
                            $info = array(
                                'username' => $users->username,
                                'href' => $users->email,
                                'msg' => $exception->getMessage()
                            );
                            act_log($info);
                        }
                    }
                    //提交事务
                    DB::commit();
                }catch (Exception $exception){
                    //回滚
                    DB::rollBack();
                    return $this->ajax_return(Code::ERROR,$exception->getMessage());
                }
                return $this->ajax_return(Code::SUCCESS,'request authorization has passed');
            }
            //回滚
            DB::rollBack();
            return $this->ajax_return(Code::ERROR,'request authorization has refused');
        }
        //修改用户申请授权信息 最大授权到期时间不得超过当前时间一年
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
        $this->post['created_at'] = strtotime($this->post['created_at']);
        $this->post['expires'] = strtotime($this->post['expires']);
        $this->post['href'] = implode('',$this->post['href']);
        if (!empty($this->post['ruleLists'])) {
            unset($this->post['ruleLists']);
        }
        $result = $this->reqRuleModel->updateResult($this->post,'id',$this->post['id']);
        if ($result) {
            return $this->ajax_return(Code::SUCCESS,'update request authorization successfully');
        }
        return $this->ajax_return(Code::SUCCESS,'update request authorization failed');
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
        $users = $this->userModel->getResult('id',$getReq->user_id);
        //获取当前用户的角色信息
        $role = $this->roleModel->getResult('id', $users->role_id);
        $auth_ids = json_decode($role->auth_ids,true);
        $auth_url = json_decode($role->auth_url,true);
        //根据用户请求授权地址获取该条规则信息
        $rule = $this->authModel->getResult('href',$getReq->href);
        //获取数key
        $arr_ids_key = count(array_keys($auth_ids,$rule->id))>0 ? array_keys($auth_ids,$rule->id)[0] : 0;
        $arr_url_key = count(array_keys($auth_url,$rule->href))>0 ? array_keys($auth_url,$rule->href)[0] : 0;
        //删除数组指定key
        if (!empty($arr_ids_key)) {
            array_splice($auth_ids, $arr_ids_key, 1);
        }
        if (!empty($arr_url_key)) {
            array_splice($auth_url,$arr_url_key,1);
        }
        DB::beginTransaction();
        try{
            $result = $this->reqRuleModel->updateResult(['status'=>2,'updated_at'=>time(),'expires'=>0,'desc'=>'权限被管理员收回'],'id',$this->post['id']);
            if ($result){
                $data = array(
                    'auth_ids' => json_encode($auth_ids),
                    'auth_url' => str_replace('\\','',json_encode($auth_url)),
                    'updated_at' => time()
                );
                $this->roleModel->updateResult($data,'id',$role->id);
            }
            DB::commit();
            return $this->ajax_return(Code::SUCCESS,'remove authorization successfully');
        }catch (Exception $exception){
            DB::rollBack();
        }
        DB::rollBack();
        return $this->ajax_return(Code::SUCCESS,'remove authorization failed');
    }
}

<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Api\CommonController;
use App\Http\Controllers\Utils\Code;
use App\Models\UserCenter;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * TODO: 用户管理
 * Class UsersController
 * @author <fl140125@gmail.com>
 * @package App\Http\Controllers\Api\v1
 */
class UsersController extends BaseController
{
    /**
     * TODO: 管理员信息
     * @return JsonResponse
     */
    public function index()
    {
        $result = $this->userModel->getResultList($this->users, $this->post['page'], $this->post['limit']);
        foreach ($result['data'] as &$item) {
            $item->updated_at = date("Y-m-d H:i:s", $item->updated_at);
            $item->created_at = date("Y-m-d H:i:s", $item->created_at);
        }
        $result['roleLists'] = in_array($this->users->role_id, [1]) ?
            $this->roleModel->getResult2('1', ['id','role_name']) : [];
        return $this->ajaxReturn(Code::SUCCESS, 'successfully', $result);
    }

    /**
     * TODO: 管理员保存
     * @return JsonResponse
     */
    public function save()
    {
        $this->validatePost($this->rule(3));
        DB::beginTransaction();
        try {
            $this->post['password'] = md5(md5($this->post['password']).$this->post['salt']);
            $this->post['remember_token'] = md5($this->post['password']);
            $this->post['status'] = $this->users->username === 'admin' ? $this->post['status'] : 2;
            $user_id = $this->userModel->addResult($this->post);
            $this->post['uid'] = config('app.client_id').$user_id;
            UserCenter::getInstance()->addResult(
                ['uid'=>$user_id,'u_name'=>$this->post['username'],'token'=>$this->post['remember_token']]
            );
            $result = $this->userModel->updateResult($this->post, 'id', $user_id);
            //同步新用户画像
            CommonController::getInstance()->updateUserAvatarUrl();
            DB::commit();
            return $result ? $this->ajaxReturn(Code::SUCCESS, 'add user successfully') :
                $this->ajaxReturn(Code::ERROR, 'add user error');
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            DB::rollBack();
            return $this->ajaxReturn(Code::ERROR, 'add user error');
        }
    }

    /**
     * TODO：获取绑定用户信息
     * @return JsonResponse
     */
    public function getBindInfo()
    {
        $this->validatePost(['remember_token'=>'required|string|size:32']);
        $result = $this->userModel->getResult('remember_token', $this->post['remember_token']);
        return !empty($result) ? $this->ajaxReturn(Code::SUCCESS, 'successfully', $result) :
            $this->ajaxReturn(Code::ERROR, 'No bound account information');
    }
    /**
     * TODO: 管理员更新
     * @param string act
     * @param integer id
     * @param string username
     * @param string password
     * @param string created_at
     * @param string updated_at
     * @return JsonResponse
     */
    public function update()
    {
        //修改用户禁用状态
        if (!empty($this->post['act'])) {
            $this->validatePost($this->rule(4), ['id.gt'=>'Permission denied']);
            unset($this->post['act']);
            $result = $this->userModel->updateResult($this->post, 'id', $this->post['id']);
            return empty($result) ? $this->ajaxReturn(Code::ERROR, 'update users status error') :
                $this->ajaxReturn(Code::SUCCESS, 'update users status successfully');
        }
        $users = $this->userModel->getResult('id', $this->post['id']);
        $this->post['created_at'] = gettype($this->post['created_at']) === 'string' ?
            strtotime($this->post['created_at']) : $this->post['created_at'];
        $this->post['updated_at'] = time();
        try {
            DB::beginTransaction();
            $this->post['password'] === $users->password ?
                $this->validatePost($this->rule(1)) :  $this->validatePost($this->rule(2));
            //用户修改密码
            $this->post['salt'] = getRoundNum(8);
            $this->post['password'] = md5(md5($this->post['password']) . $this->post['salt']);
            $this->post['remember_token'] = md5($this->post['password']); //用户修改密码后也修改当前token
            Artisan::call("longer:sync_oauth {$this->post['remember_token']}");
            DB::commit();
            return empty($result) ? $this->ajaxReturn(Code::ERROR, 'update users error') :
                $this->ajaxReturn(Code::SUCCESS, 'update users successfully');
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            DB::rollBack();
            return $this->ajaxReturn(Code::ERROR, 'update users error');
        }
    }

    /**
     * TODO:获取个人信息
     * @param string token
     * @return JsonResponse
     */
    public function center()
    {
        $result = UserCenter::getInstance()->getResult('token', $this->users->remember_token);
        $result->email = $this->users->email;
        $result->tags = empty($result->tags) ? array() : json_decode($result->tags, true);
        $result->ip_address = empty($result->ip_address) ? array() :json_decode($result->ip_address, true);
        $result->local = empty($result->local) ? array() : json_decode($result->local, true);
        return $this->ajaxReturn(Code::SUCCESS, 'successfully', $result);
    }

    /**
     * TODO:保存个人信息
     * @param string u_name 用户名
     * @param integer id
     * @param array tags 标签
     * @param integer user_status 用户状态
     * @param string ip_address IP地址
     * @param integer u_type 用户类型
     * @param string desc 描述
     * @param integer notice_status 通知状态
     * @param integer uid ID
     * @param array local 地址
     * @return JsonResponse
     */
    public function saveCenter()
    {
        $this->validatePost(
            [
                'u_name'=>'required|string',
                'id'=>'required|integer','desc'=>'required|string|max:128',
                'tags'=>'required|Array|max:128','notice_status'=>'required|integer|in:1,2',
                'user_status'=>'required|integer|in:1,2','uid'=>'required|integer',
                'ip_address' => 'required|Array','local'=>'required|Array'
            ]
        );
        unset($this->post['email']);
        $result = UserCenter::getInstance()->updateResult($this->post, 'id', $this->post['id']);
        if (!empty($result)) {
            //更新用户画像
            CommonController::getInstance()->updateUserAvatarUrl();
            return $this->ajaxReturn(Code::SUCCESS, 'update user information successfully');
        }
        return $this->ajaxReturn(Code::SUCCESS, 'update user information error');
    }

    /**
     * TODO: 删除管理员用户
     * @param integer id
     * @return JsonResponse
     */
    public function delete()
    {
        $this->validatePost(['id'=>'required|integer|gt:1'], ['id.gt'=>'Permission denied']);
        $result = $this->userModel->deleteResult('id', $this->post['id']);
        return !empty($result) ? $this->ajaxReturn(Code::SUCCESS, 'delete user successfully') :
            $this->ajaxReturn(Code::ERROR, 'delete user error');
    }

    /**
     * TODO: 验证规则
     * @param $status 1 不验证密码 （更新）  2 验证密码 （更新）  3 验证用户名（添加） 4 修改用户状态
     * @return array
     */
    protected function rule($status)
    {
        switch ($status) {
            case 1:
                $rule = [
                    'username' => 'required|max:16|string',
                    'email' => 'required|email',
                    'status'   => 'required|integer|in:1,2',
                    'phone_number' => 'required|size:11',
                    'role_id' => 'required|integer'
                ];
                break;
            case 2:
                $rule= [
                    'username' => 'required|max:16|string',
                    'email' => 'required|email',
                    'password' => 'required|string|between:6,32',
                    'salt' => 'required|string|size:8',
                    'status'   => 'required|integer|in:1,2',
                    'phone_number' => 'required|size:11',
                    'role_id' => 'required|integer'
                ];
                break;
            case 3:
                $rule= [
                    'username' => 'required|max:16|string|unique:os_users',
                    'email' => 'required|email|unique:os_users',
                    'password' => 'required|string|between:6,32',
                    'status'   => 'required|integer|in:1,2',
                    'role_id' => 'required|integer',
                    'salt' => 'required|string|size:8',
                    'created_at' => 'required|digits:10',
                    'updated_at' => 'required|digits:10'
                ];
                break;
            case 4:
                $rule = [
                    'status'   => 'required|string|in:1,2',
                    'id' => 'required|integer|gt:1'
                ];
                break;
            default:
                $rule = [];
                break;
        }
        return $rule;
    }
}

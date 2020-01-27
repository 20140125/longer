<?php

namespace App\Console\Commands;

use App\Models\UserCenter;
use App\Models\Users;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SyncOauthToUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'longer:syncOauth';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'sync oauth info to users';

    protected $usersModel;

    protected $oAuthModel;

    protected $userCenterModel;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->usersModel = Users::getInstance();
        $this->oAuthModel = \App\Models\OAuth::getInstance();
        $this->userCenterModel = UserCenter::getInstance();
    }

    /**
     * Execute the console command.
     *
     */
    public function handle()
    {
        $this->SyncOauthToUsers();
        sleep(1);
        $this->SyncUserToUserCenter();
    }

    /**
     * TODO:将Oauth同步给用户
     */
    protected function SyncOauthToUsers()
    {
        $oauthArr = $this->oAuthModel->getOauthLists();
        foreach ($oauthArr as $oauth) {
            //同步用户数据
            $users = $this->usersModel->getResult('id', $oauth->uid);
            if (!empty($users)) {
                $this->usersModel->updateResult(['remember_token' => $oauth->remember_token], 'id', $oauth->uid);
                $this->info('修改用户[' . $oauth->username . ']信息成功');
            } else {
                $salt = get_round_num(8);
                $arr = [
                    'username' => $oauth->username,
                    'avatar_url' => $oauth->avatar_url,
                    'remember_token' => $oauth->remember_token,
                    'email' => empty($oauth->email) ? '' : $oauth->email,
                    'salt' => $salt,
                    'password' => md5(md5('123456789') . $salt),
                    'role_id' => $oauth->role_id,
                    'ip_address' => request()->ip(),
                    'created_at' => time(),
                    'updated_at' => time(),
                    'status' => $oauth->status,
                    'phone_number' => '',
                    'uuid' => md5($oauth->username).uniqid()
                ];
                $uid = $this->usersModel->addResult($arr);
                $this->oAuthModel->updateResult(['uid' => $uid], 'id', $oauth->id);
                $this->info('添加用户[' . $oauth->username . ']信息成功');
            }
        }
    }

    /**
     * TODO:将用户同步到用户中心
     */
    public function SyncUserToUserCenter()
    {
        $users = DB::table('os_users')->get();
        foreach ($users as $user) {
            //同步用户基础信息数据
            $userCenter = $this->userCenterModel->getResult('uid',$user->id);
            if (!empty($userCenter)) {
                $this->userCenterModel->updateResult(['token'=>$user->remember_token,'u_name'=>$user->username],'uid',$user->id);
                $this->info('修改用户['.$user->username.']基础信息成功');
            } else {
                $arr = [
                    'u_name' => $user->username,
                    'token' => $user->remember_token,
                    'uid' => $user->id,
                    'notice_status' => 1,
                    'user_status' => 1,
                ];
                $this->userCenterModel->addResult($arr);
                $this->info('添加用户['.$user->username.']基础信息成功');
            }
        }
    }
}

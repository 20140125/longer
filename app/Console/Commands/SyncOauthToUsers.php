<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\CommonController;
use App\Models\UserCenter;
use App\Models\Users;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncOauthToUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'longer:sync_oauth';

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
        //更新用户画像
        $commonContr = new CommonController();
        $commonContr->updateUserAvatarUrl();
    }

    /**
     * TODO:将Oauth同步给用户
     */
    protected function SyncOauthToUsers()
    {
        $oauthArr = $this->oAuthModel->getOauthLists();
        $bar = $this->getOutput()->createProgressBar(count($oauthArr));
        foreach ($oauthArr as $oauth) {
            //同步用户数据
            $users = $this->usersModel->getResult('id', $oauth->uid);
            if (!empty($users)) {
                $this->usersModel->updateResult(['remember_token' => $oauth->remember_token], 'id', $oauth->uid);
                $bar->advance();
                $this->info('修改用户[' . $oauth->username . ']信息成功');
            } else {
                $salt = get_round_num(8);
                $rand_str = get_xing_lists(); //避免用户名重复
                $username = $this->usersModel->getResult('username', $oauth->username,'=',['username']);
                $arr = [
                    'username' => !empty($username) ? $oauth->username.'_'.$rand_str[rand(0,count($rand_str))] : $oauth->username,
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
                $bar->advance();
                $this->info('添加用户[' . $oauth->username . ']信息成功');
            }
        }
        $bar->finish();
    }

    /**
     * TODO:将用户同步到用户中心
     */
    protected function SyncUserToUserCenter()
    {
        $users = DB::table('os_users')->get();
        $bar = $this->getOutput()->createProgressBar(count($users));
        foreach ($users as $user) {
            //同步用户基础信息数据
            $userCenter = $this->userCenterModel->getResult('uid',$user->id);
            if (!empty($userCenter)) {
                $this->userCenterModel->updateResult(['token'=>$user->remember_token,'u_name'=>$user->username],'uid',$user->id);
                $bar->advance();
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
                $bar->advance();
                $this->info('添加用户['.$user->username.']基础信息成功');
            }
        }
        $bar->finish();
    }
}

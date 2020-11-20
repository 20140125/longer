<?php

namespace App\Console\Commands;

use App\Http\Controllers\Api\CommonController;
use App\Models\UserCenter;
use App\Models\Users;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * @author <fl140125@gmail.com>
 * Class SyncOauthToUsers
 * @package App\Console\Commands
 */
class SyncOauthToUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string $signature
     */
    protected $signature = 'longer:sync_oauth {remember_token=default}';

    /**
     * The console command description.
     *
     * @var string $description
     */
    protected $description = 'sync oauth info to users';
    /**
     * @var Users $usersModel
     */
    protected $usersModel;
    /**
     * @var \App\Models\OAuth  $oAuthModel
     */
    protected $oAuthModel;
    /**
     * @var UserCenter $userCenterModel
     */
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
        $this->syncOauthToUsers();
        sleep(1);
        $this->syncUserToUserCenter();
        //更新用户画像
        CommonController::getInstance()->updateUserAvatarUrl();
        $this->info('同步用户画像成功');
    }

    /**
     * TODO:将Oauth同步给用户
     */
    protected function syncOauthToUsers()
    {
        $where = [];
        if ($this->argument('remember_token') !== 'default') {
            $where[] =  ['remember_token', '=',$this->argument('remember_token')];
        }
        $oauthArr = $this->oAuthModel->getOauthLists($where);
        $bar = $this->getOutput()->createProgressBar(count($oauthArr));
        foreach ($oauthArr as $oauth) {
            //同步用户数据
            $users = $this->usersModel->getResult('id', $oauth->uid);
            if (!empty($users)) {
                $this->usersModel->updateResult(['remember_token' => $oauth->remember_token], 'id', $oauth->uid);
                $bar->advance();
                $this->info('修改用户[' . $oauth->username . ']信息成功');
            } else {
                $salt = getRoundNum(8);
                $arr = [
                    'username' => $oauth->username,
                    'avatar_url' => $oauth->avatar_url,
                    'remember_token' => $oauth->remember_token,
                    'email' => empty($oauth->email) ? '' : $oauth->email,
                    'salt' => $salt,
                    'password' => md5(md5('123456789') . $salt),
                    'role_id' => $oauth->role_id,
                    'ip_address' => getServerIp(),
                    'created_at' => time(),
                    'updated_at' => time(),
                    'status' => $oauth->status,
                    'phone_number' => '',
                    'uuid' => ''
                ];
                $id = $this->usersModel->addResult($arr);
                $this->usersModel->updateResult(['uuid'=>config('app.client_id').$id], 'id', $id);
                $this->oAuthModel->updateResult(['uid' => $id], 'id', $oauth->id);
                $bar->advance();
                $this->info('添加用户[' . $oauth->username . ']信息成功');
            }
        }
        $bar->finish();
    }

    /**
     * TODO:将用户同步到用户中心
     */
    protected function syncUserToUserCenter()
    {
        $where = [];
        if ($this->argument('remember_token') !== 'default') {
            $where[] =  ['remember_token', '=',$this->argument('remember_token')];
        }
        $users = $this->usersModel->getAll($where, ["*"]);
        $bar = $this->getOutput()->createProgressBar(count($users));
        foreach ($users as $user) {
            //同步用户基础信息数据
            $userCenter = $this->userCenterModel->getResult('uid', $user->id);
            if (!empty($userCenter)) {
                $this->userCenterModel->updateResult(
                    ['token'=>$user->remember_token,'u_name'=>$user->username,'type'=>'sync'],
                    'uid',
                    $user->id
                );
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

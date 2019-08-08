<?php

namespace App\Console\Commands;

use App\Http\Controllers\Oauth\BaiDuController;
use App\Http\Controllers\Oauth\GiteeController;
use App\Http\Controllers\Oauth\QQController;
use Illuminate\Console\Command;
use App\Models\OAuth as oauthModel;

/**
 * Class Oauth
 * @author <fl140125@gmail.com>
 * @package App\Console\Commands
 */
class Oauth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'oauth:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'refresh oauth token';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->getOauth();
    }

    /**
     *
     */
    private function getOauth()
    {
        $where[] = ['expires','<',time()];
        $where[] = ['refresh_token','<>',''];
        $result = oauthModel::getInstance()->getOauthLists($where,['expires','refresh_token','id','oauth_type']);
        if (count($result) == 0) {
            $this->warn('access_token already exists');
            return;
        }
        foreach ($result as $item) {
            $this->refreshToken($item->oauth_type,$item->refresh_token);
        }
    }

    /**
     * TODO：刷新 access_token
     * @param string $oauth_type 账号来源
     * @param string $refresh_token 用于刷新 access_token
     */
    private function refreshToken($oauth_type,$refresh_token)
    {
        switch ($oauth_type) {
            case 'qq':
                try {
                    $appId = config('app.qq_appid');
                    $appSecret = config('app.qq_secret');
                    $result = QQController::getInstance($appId,$appSecret)->refreshToken($refresh_token);
                    if ($result['code'] !== 201) {
                        $where[] = ['oauth_type',$oauth_type];
                        $where[] = ['refresh_token',$refresh_token];
                        $result->remember_token = md5(md5($result->remember_token).time());
                        $oauth = oauthModel::getInstance()->updateResult(object_to_array($result),$where);
                        if ($oauth) {
                            $this->info('update  qq oauth success');
                            return ;
                        }
                        $this->warn('update qq oauth failed');
                        return ;
                    }
                    $this->warn(json_encode($result));
                } catch (\Exception $e) {
                    $this->error($e->getMessage().' qq');
                }
                break;
            case 'gitee':
                try{
                    $appId = config('app.gitee_appid');
                    $appSecret = config('app.gitee_secret');
                    $result = GiteeController::getInstance($appId,$appSecret)->refreshToken($refresh_token);
                    if ($result['code'] !== 201) {
                        $where[] = ['oauth_type',$oauth_type];
                        $where[] = ['refresh_token',$refresh_token];
                        $result->remember_token = md5(md5($result->remember_token).time());
                        $oauth = oauthModel::getInstance()->updateResult(object_to_array($result),$where);
                        if ($oauth) {
                            $this->info('update gitee oauth success');
                            return ;
                        }
                        $this->warn('update  gitee oauth failed');
                        return ;
                    }
                    $this->warn(json_encode($result));
                }catch (\Exception $exception){
                    $this->error($exception->getMessage().' gitee');
                }
                break;
            case 'baidu':
                try{
                    $appId = config('app.baidu_appid');
                    $appSecret = config('app.baidu_secret');
                    $result = BaiDuController::getInstance($appId,$appSecret)->refreshToken($refresh_token);
                    if ($result['code'] !== 201) {
                        $where[] = ['oauth_type',$oauth_type];
                        $where[] = ['refresh_token',$refresh_token];
                        $result->remember_token = md5(md5($result->remember_token).time());
                        $oauth = oauthModel::getInstance()->updateResult(object_to_array($result),$where);
                        if ($oauth) {
                            $this->info('update baidu oauth success');
                            return ;
                        }
                        $this->warn('update baidu oauth failed');
                        return ;
                    }
                    $this->warn(json_encode($result));
                }catch (\Exception $exception){
                    $this->error($exception->getMessage().' baidu');
                }
                break;
            case 'github':
            case 'weibo':
                $this->error('refresh_token does not exists');
                break;
            default:
                $this->error('账户来源不存在');
                break;
        }
    }
}

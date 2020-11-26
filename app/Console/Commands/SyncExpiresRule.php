<?php

namespace App\Console\Commands;

use App\Http\Controllers\Utils\Code;
use App\Http\Controllers\Utils\RedisClient;
use App\Models\Auth;
use App\Models\Push;
use App\Models\ReqRule;
use App\Models\Role;
use App\Models\UserCenter;
use App\Models\Users;
use Illuminate\Console\Command;

/**
 * Class ExpiresRule
 * @author <fl140125@gmail.com>
 * @package App\Console\Commands
 */
class SyncExpiresRule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string $signature
     */
    protected $signature = 'longer:sync-expires-rule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'expires rule station information push';
    /**
     * @var ReqRule $reqRuleModel
     */
    protected $reqRuleModel;
    /**
     * @var Role $roleModel
     */
    protected $roleModel;
    /**
     * @var Users $userModel
     */
    protected $userModel;
    /**
     * @var RedisClient $redisClient
     */
    protected $redisClient;
    /**
     * @var Auth $authModel
     */
    protected $authModel;

    /**
     * Create a new command instance.
     * ReqAuth constructor.
     */
    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set("Asia/Shanghai");
        $this->reqRuleModel = ReqRule::getInstance();
        $this->roleModel = Role::getInstance();
        $this->userModel = Users::getInstance();
        $this->authModel = Auth::getInstance();
        $this->redisClient = RedisClient::getInstance();
    }
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->getExpiresRule();
    }

    /**
     * TODO:同步过期权限站内信通知
     * @return bool|void
     */
    private function getExpiresRule()
    {
        $where[] = ['status',1];
        $where[] = ['expires','<=',time()];
        $result = $this->reqRuleModel->getCommandRule($where);
        $bar = $this->output->createProgressBar(count($result));
        foreach ($result as &$item) {
            $userCenter = UserCenter::getInstance()->getResult('uid', $item->user_id);
            if ($userCenter->notice_status == '2') {
                $this->error("　".$item->username .'　已禁用站内信通知');
                return false;
            }
            //获取当前申请授权用户信息
            $users = $this->userModel->getResult('id', $item->user_id);
            //获取当前用户的角色信息
            $role = $this->roleModel->getResult('id', $users->role_id);
            $auth_ids = json_decode($role->auth_ids, true);
            $auth_url = json_decode($role->auth_url, true);
            //根据用户请求授权地址获取该条规则信息
            $rule = $this->authModel->getResult('href', $item->href);
            //获取数key
            $arr_ids_key = count(array_keys($auth_ids, $rule->id))>0 ? array_keys($auth_ids, $rule->id)[0] : 0;
            $arr_url_key = count(array_keys($auth_url, $rule->href))>0 ? array_keys($auth_url, $rule->href)[0] : 0;
            //删除数组指定key
            if (!empty($arr_ids_key)) {
                array_splice($auth_ids, $arr_ids_key, 1);
            }
            if (!empty($arr_url_key)) {
                array_splice($auth_url, $arr_url_key, 1);
            }
            $data = array(
                'auth_ids' => json_encode($auth_ids),
                'auth_url' => str_replace('\\', '', json_encode($auth_url)),
                'updated_at' => time()
            );
            $result = $this->roleModel->updateResult($data, 'id', $role->id);
            if (!empty($result)) {
                //告诉用户权限已经过期
                $message = array(
                    'username' => $item->username,
                    'info' => '您有权限('.config('app.url').str_replace('admin/', 'api/v1/', $item->href).')已经过期,
                    过期时长('.diffTimes(time(), $item->expires).')，如有需要请前往申请权限列表续期~！',
                    'uid'  => Users::getInstance()->getResult('id', $item->user_id, '=', ['uuid'])->uuid,
                    'state' => Code::WEBSOCKET_STATE[1],
                    'status' => 1,
                    'created_at' => time()
                );
                try {
                    if ($this->redisClient->sIsMember(config('app.redis_user_key'), $message['uid'])) {
                        if (webPush($message['info'], $message['uid'])) {
                            $message['state'] = Code::WEBSOCKET_STATE[0];
                        }
                    } else {
                        $message['state'] = Code::WEBSOCKET_STATE[2];
                    }
                    Push::getInstance()->addResult($message);
                    //过期权限需要重新授权
                    $item->status = 2;
                    $item->expires = 0;
                    $item->updated_at = time();
                    $item->desc = '权限被管理员收回';
                    $this->reqRuleModel->updateResult(objectToArray($item), 'id', $item->id);
                } catch (\Exception $e) {
                    $this->error($e->getMessage());
                }
                $this->info("权限接口 ".config('app.url').str_replace('admin/', 'api/v1/', $item->href).',
                已经过期('.diffTimes($item->expires, time()).')，现已撤销回收');
            } else {
                $this->info("权限接口 ".config('app.url').str_replace('admin/', 'api/v1/', $item->href).'),
                还有('.diffTimes($item->expires, time()).')过期');
            }
            sleep(0.5);
            $bar->advance();
        }
        $bar->finish();
        $this->info('  successfully!');
    }
}

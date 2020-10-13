<?php

namespace App\Console\Commands;

use App\Http\Controllers\Utils\Code;
use App\Http\Controllers\Utils\RedisClient;
use App\Models\Push;
use App\Models\ReqRule;
use App\Models\UserCenter;
use Illuminate\Console\Command;
/**
 * Class NormalRule
 * @author <fl140125@gmail.com>
 * @package App\Console\Commands
 */
class NormalRule extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'longer:normal_rule';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'normal rule station information push';
    /**
     * @var ReqRule $reqRuleModel
     */
    protected $reqRuleModel;
    /**
     * @var RedisClient $redisClient
     */
    protected $redisClient;


    /**
     * Create a new command instance.
     * ReqAuth constructor.
     */
    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set("Asia/Shanghai");
        $this->reqRuleModel = ReqRule::getInstance();
        $this->redisClient = RedisClient::getInstance();
    }
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->getNormalRule();
    }

    /**
     * TODO:同步即将过期权限站内信通知
     * @return bool|void
     */
    private function getNormalRule()
    {
        $where[] = ['status',1];
        $where[] = ['expires','>',time()];
        $result = $this->reqRuleModel->getCommandRule($where);
        $bar = $this->output->createProgressBar(count($result));
        foreach ($result as $item) {
            $userCenter = UserCenter::getInstance()->getResult('u_name',$item->username);
            if ($userCenter->notice_status == '2') {
                $this->error("　".$item->username .'　已禁用站内信通知');
                return false;
            }
            //权限还有要过期提醒用户关注
            if ($item->expires< time() + 3600*24*3) {
                //告诉用户权限已经过期
                $message = array(
                    'username' => $item->username,
                    'info' => '您有权限('.config('app.url').str_replace('admin/','api/v1/',$item->href).')即将过期,还有('.diff_times($item->expires,time()).')，如有需要请前往个人中心续期~！',
                    'uid'  => md5($item->username),
                    'state' => Code::WebSocketState[1],
                    'status' => 1,
                    'created_at' => time()
                );
                try {
                    if ($this->redisClient->sIsMember(config('app.redis_user_key'),$message['uid'])) {
                        if (web_push($message['info'], $message['uid'])) {
                            $message['state'] = Code::WebSocketState[0];
                        }
                    } else {
                        $message['state'] = Code::WebSocketState[2];
                    }
                    Push::getInstance()->addResult($message);
                } catch (\ErrorException $e) {
                    $this->error($e->getMessage());
                }
                $this->info(' 站内信发送成功');
                sleep(0.5);
                $bar->advance();
            } else {
                $this->error(' 暂无记录');
            }
        }
        $bar->finish();
        $this->info('  successfully!');
    }
}
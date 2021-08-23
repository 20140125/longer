<?php

namespace App\Console\Commands;

use App\Http\Controllers\Utils\RedisClient;
use Illuminate\Console\Command;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'longer:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
     * @return void
     */
    public function handle()
    {
        $redisUser = RedisClient::getInstance()->sMembers(config('app.redis_user_key'));
        $users = json_decode(RedisClient::getInstance()->sMembers(config('app.chat_user_key'))[0], true);
        foreach ($users as $key => $item) {
            $users[$key]['unread_count'] = 0;
            $users[$key]['type'] = 'login';
            $users[$key]['room_id'] = '1200';
            foreach ($redisUser as $redis) {
                $users[$key]['online'] = $redis === $users[$key]['uuid'];
            }
            $unreadMsg = RedisClient::getInstance()->hGetAll('unread_' . $users[$key]['uuid']) ?? [];
            /* 单条用户展示未读消息数 */
            $unreadArr = array();
            foreach ($unreadMsg as $from => $total) {
                $unreadArr[] = ['form' => $from, 'total' => $total];
                $users[$key]['unread_count'] += $total;
            }
            /* 未读消息数 */
            $users[$key]['unread'] = $unreadArr;
            $arr[$users[$key]['uuid']] = $users[$key];
            $sort[$users[$key]['uuid']] = $users[$key]['online'];
        }
        array_multisort($sort, SORT_DESC, $arr);
        $this->info(json_encode($arr, JSON_UNESCAPED_UNICODE));
    }
}

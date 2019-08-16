<?php

namespace App\Console\Commands\worker;

use Curl\Curl;
use Illuminate\Console\Command;
use Workerman\Worker;
use PHPSocketIO\SocketIO;

class AuthServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'WorkerMan:auth';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'auth server';
    /**
     * @var Curl $curl
     */
    protected $curl;
    //PHPSocketIO服务
    private static $senderIo = null;

    /**
     * Create a new command instance.
     * AuthServer constructor.
     * @throws \ErrorException
     */
    public function __construct()
    {
        parent::__construct();
        $this->curl = new Curl();
        self::$senderIo = new SocketIO(2021);
    }

    /**
     *
     */
    public function handle()
    {
        // 客户端发起连接事件时，设置连接socket的各种事件回调
        self::$senderIo->on('connection',function ($socket){
            //客户端发送消息
            $socket->on('notification from web',function ($msg) use ($socket){
                $socket->addedUser = false;
                $socket->emit('notification from server', array(
                    'username'=> $socket->username,
                    'message'=> $msg
                ));
            });
            //客户端添加用户
            $socket->on('add user', function ($username) use($socket){
                global $userNames, $numUsers;
                $socket->username = $username;
                $userNames[$username] = $username;
                ++$numUsers;
                $socket->addedUser = true;
                $socket->emit('login', array(
                    'numUsers' => $numUsers
                ));
                $socket->broadcast->emit('user joined', array(
                    'username' => $socket->username,
                    'numUsers' => $numUsers
                ));
            });
            $socket->on('typing', function () use($socket) {
                $socket->emit('typing', array(
                    'username' => $socket->username
                ));
            });
            $socket->on('stop typing', function () use($socket) {
                $socket->emit('stop typing', array(
                    'username' => $socket->username
                ));
            });
            //客户端用户下线
            $socket->on('disconnect', function () use($socket) {
                global $userNames, $numUsers;
                if($socket->addedUser) {
                    unset($userNames[$socket->username]);
                    --$numUsers;
                    $socket->emit('user left', array(
                        'username' => $socket->username,
                        'numUsers' => $numUsers
                    ));
                }
            });
        });
        //当$io启动后监听一个http端口，通过这个端口可以给任意uid或者所有uid推送数据
        self::$senderIo->on('workerStart',function (){
            $httpWorker = new Worker('http://www.laravel.com:2021');
            $httpWorker->onMessage = function ($connection,$data) {
                global $userNames;
                $type=$data['post']['type'] ?? '';
                switch ($type) {
                    case 'publish':
                        $to = $data['post']['to'] ?? '';
                        $context = htmlspecialchars($data['post']['context'] ?? '');
                        if ($to) {
                            self::$senderIo->to($to)->emit('new message', $context);
                        } else {
                            self::$senderIo->emit('new message', $context);
                        }
                        // http接口返回，如果用户离线socket返回fail
                        if ($to && !isset($userNames[$to])) {
                            return $connection->send('offline');
                        } else {
                            return $connection->send('ok');
                        }
                        break;
                }
                return $connection->send('fail');
            };
            //执行监听
            $httpWorker->listen();
        });
        if(!defined('GLOBAL_START')) {
            Worker::runAll();
        }
    }
}

<?php

namespace App\Console\Commands;

use App\Http\Controllers\Utils\Rsa;
use Curl\Curl;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

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
        /* 公钥加密 */
        $rsa = Rsa::getInstance();
        $encrypt = $rsa->privateEncrypt('{"code":20000,"message":"successfully","lists":[{"id":1,"pid":0,"name":"权限管理","href":"/admin/auth/default"},{"id":2,"pid":0,"name":"文件管理","href":"/admin/file/default"},{"id":3,"pid":0,"name":"系统管理","href":"/admin/system/default"},{"id":4,"pid":0,"name":"接口文档","href":"/admin/interface/default"},{"id":5,"pid":0,"name":"用户管理","href":"/admin/users/default"},{"id":6,"pid":0,"name":"数据库管理","href":"/admin/database/default"},{"id":7,"pid":0,"name":"日志管理","href":"/admin/log/default"},{"id":8,"pid":1,"name":"权限列表","href":"/admin/auth/index"},{"id":11,"pid":1,"name":"角色列表","href":"/admin/role/index"},{"id":14,"pid":1,"name":"授权列表","href":"/admin/permission/index"},{"id":17,"pid":2,"name":"文件列表","href":"/admin/file/index"},{"id":27,"pid":3,"name":"系统配置","href":"/admin/config/index"},{"id":30,"pid":3,"name":"系统通知","href":"/admin/push/index"},{"id":34,"pid":7,"name":"系统日志","href":"/admin/log/index"},{"id":36,"pid":5,"name":"用户列表","href":"/admin/users/index"},{"id":39,"pid":5,"name":"授权用户","href":"/admin/oauth/index"},{"id":41,"pid":5,"name":"会员中心","href":"/admin/userCenter/index"},{"id":43,"pid":6,"name":"数据表 列表","href":"/admin/database/index"},{"id":48,"pid":3,"name":"城市列表","href":"/admin/area/index"},{"id":52,"pid":51,"name":"会员登录","href":"/admin/account/login"},{"id":53,"pid":51,"name":"验证码上报","href":"/admin/report/code"},{"id":54,"pid":51,"name":"登录鉴权","href":"/admin/check/authorized"},{"id":55,"pid":51,"name":"邮件发送","href":"/admin/mail/send"},{"id":56,"pid":51,"name":"授权配置","href":"/admin/oauth/config"},{"id":57,"pid":4,"name":"接口列表","href":"/admin/interfaceCategory/index"},{"id":64,"pid":51,"name":"用户权限","href":"/admin/common/menu"},{"id":66,"pid":3,"name":"计划列表","href":"/admin/timeline/index"},{"id":70,"pid":3,"name":"系统工具","href":"/admin/tools/index"},{"id":72,"pid":3,"name":"同步工具","href":"/admin/spider/index"},{"id":74,"pid":51,"name":"表情列表","href":"/admin/emotion/index"}]}');
        $this->info($encrypt);
        $decrypt = $rsa->publicDecrypt($encrypt);
        $this->info($decrypt);
    }
}

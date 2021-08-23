<?php

/**
 * run with command
 * php start.php start
 */

use Workerman\Worker;

// composer 的 autoload 文件
require_once '../../vendor/autoload.php';

if (strpos(strtolower(PHP_OS), 'win') === 0) {
    exit("start.php not support windows, please use start_for_win.bat\n");
}

// 标记是全局启动  linux 启动时需要先启动laravel脚本 （App\Console\Commands\SyncOauthToUsers）  php artisan longer:sync_oauth 同步好友画像存在redis
define('GLOBAL_START', 1);
// 加载IO
foreach (glob(__DIR__ . '/start*.php') as $start_file) {
    require_once "{$start_file}";
}
// 运行所有服务
Worker::runAll();

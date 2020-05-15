CHCP 65001
#先同步用户画像，展示好友列表
cd ../../
php artisan longer:sync_oauth
#再启动长链接脚本
cd public/webPush
php start_io.php start_businessworker.php  start_gateway.php start_register.php
pause

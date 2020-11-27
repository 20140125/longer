CHCP 65001

cd ../../
php artisan longer:sync-oauth

cd public/webPush
php start_io.php start_businessworker.php  start_gateway.php start_register.php
pause

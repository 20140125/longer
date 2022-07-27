<?php
/**
 * @class SyncPermissionApply
 * @author <fl2014@gmail.com>
 * @created_time 2022-07-27 11:00:12
 * @modified_by v_llongfang
 * @modified_time 2022-07-27 11:00:12
 */

namespace App\Console\Commands;

use App\Http\Controllers\Service\v1\BaseService;
use App\Http\Controllers\Service\v1\PermissionApplyService;
use App\Http\Controllers\Utils\Code;
use App\Models\Api\v1\PermissionApply;
use App\Models\Api\v1\PermissionApplyLog;
use App\Models\Api\v1\Role;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncPermissionApply extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'longer:sync-permission_apply';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'sync users permission apply';

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
     *
     * @return void
     */
    public function handle(): void
    {
        $this->getPermissionApplyLists();
    }

    protected function getPermissionApplyLists()
    {
        $lists = PermissionApply::getInstance()->getLists([], [['expires', '<', time()], ['status', '=', 1]]);
        foreach ($lists['data'] as &$item)
        {
            $form = PermissionApplyService::getInstance()->getRoleAuth($item->id, 2);
            DB::beginTransaction();
            try {
                $result = PermissionApply::getInstance()->updateOne(['id' => $form['request_auth_id']], ['status' => 2, 'updated_at' => time(), 'expires' => 0]);
                if (empty($result)) {
                    $this->info('权限已经回收：'.$form['request_auth_id']);
                    DB::rollBack();
                }
                $result = Role::getInstance()->updateOne(['id' => $form['user']->role_id], $form['form']);
                $log = [
                    'apply_id' => $form['request_auth_id'],
                    'desc' => '系统收回用户权限',
                    'user_name' => 'Admin',
                    'created_at' => date(time()),
                    'user_id' => 1
                ];
                PermissionApplyLog::getInstance()->saveOne($log);
                $this->info(json_encode($log, JSON_UNESCAPED_UNICODE));
                DB::commit();
            } catch (\Exception $exception) {
                $this->error($exception->getMessage());
                DB::rollBack();
            }
        }
    }
}

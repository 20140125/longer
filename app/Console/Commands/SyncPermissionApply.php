<?php
/**
 * @class SyncPermissionApply
 * @author <fl2014@gmail.com>
 * @created_time 2022-07-27 11:00:12
 * @modified_by v_llongfang
 * @modified_time 2022-07-27 11:00:12
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;

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

    }
}

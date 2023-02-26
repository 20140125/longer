<?php

namespace App\Console\Commands;

use App\Models\Api\v1\Role;
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
    protected $description = 'Testing';

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
     */
    public function handle()
    {
        $roleLists = Role::getInstance()->getLists([]);
        foreach ($roleLists['data'] as $item) {
            $item->created_at = date("Y-m-d H:i:s", $item->created_at);
            $item->updated_at = date("Y-m-d H:i:s", $item->updated_at);
        }
        $this->info(json_encode($roleLists, JSON_UNESCAPED_UNICODE));
    }
}

<?php

namespace App\Console\Commands;

use App\Http\Controllers\Service\MiniProgram\ImageService;
use App\Models\Api\v1\Oauth;
use App\Models\Api\v1\UserCenter;
use App\Models\Api\v1\Users;
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
        $columns = ['href', 'name', 'width', 'height', 'id'];
        $result = ImageService::getInstance()->getImageLists([], ['page' => 1, 'limit' => 10], ['order' => 'rand', 'direction' => 'desc'], $columns);
        $this->info(json_encode($result));
    }
}

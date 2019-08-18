<?php

namespace App\Console\Commands;

use App\Models\ReqRule;
use App\Models\Role;
use App\Models\OAuth;
use Illuminate\Console\Command;

/**
 * Class ReqAuth
 * @author <fl140125@gmail.com>
 * @package App\Console\Commands
 */
class ReqAuth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'longer:set_auth';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'set oauth Unauthorized';
    /**
     * @var ReqRule $reqRuleModel
     */
    protected $reqRuleModel;
    /**
     * @var Role $roleModel
     */
    protected $roleModel;
    /**
     * @var OAuth $oauthModel
     */
    protected $oauthModel;

    /**
     * Create a new command instance.
     * ReqAuth constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->reqRuleModel = ReqRule::getInstance();
        $this->roleModel = Role::getInstance();
        $this->oauthModel = OAuth::getInstance();
    }
    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //
    }

    private function getReqRule()
    {
        $where[] = ['status',1];
        $where[] = ['expires','<=',time()];

    }
}

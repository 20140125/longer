<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class MusicSearch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'music:search';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'music search description';

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
        try{
            $where[] = array('status','=','2');
            $where[] = array('updated_at','<',date('Y-m-d H:i:s',strtotime('-1 day')));
            $musicSearch = DB::table('os_music_user_search')->where($where)->get(['id']);
            $ids = array();
            foreach ($musicSearch as $item){
                $ids[] = $item->id;
            }
            $rs = DB::table('os_music_user_search')->whereIn('id',$ids)->update(array('status'=>'1','updated_at'=>date("Y-m-d H:i:s")));
            if (!empty($rs)){
                $this->info('数据修改成功：'.date('Y-m-d H:i:s'));
            }else{
                $this->info('修改修改失败：'.date('Y-m-d H:i:s'));
            }
        }catch (\Exception $exception){
            $this->error($exception->getMessage().'：'.date('Y-m-d H:i:s'));
        }
    }
}

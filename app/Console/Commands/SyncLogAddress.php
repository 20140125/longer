<?php

namespace App\Console\Commands;

use App\Http\Controllers\Utils\AMap;
use App\Models\Api\v1\Log;
use Illuminate\Console\Command;

class SyncLogAddress extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'longer:sync-log_address';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'From the high moral API to synchronize address local log';

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
        $this->syncLogAddress();
    }

    /**
     * 同步日志地址
     */
    protected function syncLogAddress()
    {
        try {
            $lists = Log::getInstance()->getLists([], ['page' => 1, 'limit' => 10], true);
            $bar = $this->output->createProgressBar(count($lists));
            foreach ($lists as &$item) {
                if (in_array($item->ip_address, ['127.0.0.1', '192.168.255.10'])) {
                    $item->local = '中华人民共和国';
                } else {
                    $res = (array)AMap::getInstance()->getAddress($item->ip_address);
                    $province = gettype($res['province']) === 'string' ? $res['province'] : '中华人民共和国';
                    $city = gettype($res['city']) === 'string' ? $res['city'] : '';
                    $item->local = $province . ',' . $city;
                }
                if (!empty(Log::getInstance()->updateOne(['id' => $item->id], ['local' => $item->local]))) {
                    $this->info('Successfully updated location：' . $item->local);
                } else {
                    $this->info('Failed updated location：' . $item->local);
                }
                usleep(rand(500000, 700000));
                $bar->advance();
            }
            $this->info('Successfully synchronized location');
            $bar->finish();
        } catch (\Exception $exception) {
            $this->error($exception);
        }
    }
}

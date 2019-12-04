<?php

namespace App\Http\Controllers\Api\v1;

use App\Exports\ExcelExport;
use App\Http\Controllers\Utils\Code;
use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Class ExcelController
 * @author <fl140125@gmail.com>
 * @package App\Http\Controllers\Api\v1
 */
class ExcelController extends BaseController
{
    /**
     * TODO：数据导出
     * @return JsonResponse
     */
    public function export()
    {
        if (!is_dir(storage_path('app/excel/'))) {
            mkdir(storage_path('app/excel/'));
        }
        $result = Excel::store(new ExcelExport($this->post), '/excel/'.date('Ymd').'_'.$this->post['table'].'.xls');
        if ($result) {
            return $this->ajax_return(Code::SUCCESS,'excel '.$this->post['table'].'.xls export successfully',['href'=>storage_path('app/excel/').date('Ymd').'_'.$this->post['table'].'.xls']);
        }
        return $this->ajax_return(Code::ERROR,'excel '.$this->post['table'].'.xls export failed');
    }
    public function import()
    {

    }
}

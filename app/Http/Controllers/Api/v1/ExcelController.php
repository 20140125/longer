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
        if (!is_dir(storage_path('app/public/'))) {
            mkdir(storage_path('app/public/'));
        }
        $result = Excel::store(new ExcelExport($this->post), '/public/'.date('Ymd').'/'.$this->post['table'].'.xls');
        if ($result) {
            return $this->ajaxReturn(
                Code::SUCCESS,
                'excel '.$this->post['table'].'.xls export successfully',
                [
                    'href'=> storage_path('app/public/').date('Ymd').'/'.$this->post['table'].'.xls',
                    'name' => $this->post['table'].'.xls',
                ]
            );
        }
        return $this->ajaxReturn(Code::ERROR, 'excel '.$this->post['table'].'.xls export failed');
    }
    /**
     * TODO：数据导入(直接入库)
     */
    public function import()
    {
        return $this->ajaxReturn(Code::SUCCESS, 'successfully');
    }
}

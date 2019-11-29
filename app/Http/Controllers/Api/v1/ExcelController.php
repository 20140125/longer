<?php

namespace App\Http\Controllers\Api\v1;

use App\Exports\ExcelExport;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Class ExcelController
 * @author <fl140125@gmail.com>
 * @package App\Http\Controllers\Api\v1
 */
class ExcelController extends BaseController
{
    /**
     * TODO：数据导出
     * @return BinaryFileResponse
     */
    public function export()
    {
        $result = Excel::download(new ExcelExport($this->post),'a.xls');
        return $result;
    }
    public function import()
    {

    }
}

<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

/**
 * Class ExcelExport
 * @author <fl140125@gmail.com>
 * @package App\Exports
 */
class ExcelExport implements FromCollection,WithHeadings,ShouldAutoSize
{
    /**
     * @var array $request
     */
    protected $request;
    /**
     * ExcelExport constructor.
     * @param $request
     */
    public function __construct($request)
    {
        $this->request = $request;
    }

    /**
     * TODO:设置表头
     * @return array
     */
    public function headings(): array
    {
        return $this->tableMapping();
    }

    /**
     * TODO：数据格式化
     * @return Collection|array
     */
    public function collection()
    {
        return DB::table($this->request['table'])->get();
    }

    /**
     * TODO：获取数据表注释
     * @return array
     */
    protected function tableMapping()
    {
        $mapping = array();
        $columnsObj  = DB::select(sprintf("SHOW FULL COLUMNS FROM %s", $this->request['table']));
        foreach ($columnsObj as $columns) {
            //字段存在备注的时候
            if (!empty($columns->Comment)) {
                array_push($mapping,$columns->Comment);
            }
            //字段不存在备注的时候
            if (empty($columns->Comment)) {
                array_push($mapping,$columns->Field);
            }
        }
        return $mapping;
    }
}

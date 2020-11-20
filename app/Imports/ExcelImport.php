<?php

namespace App\Imports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithMappedCells;

class ExcelImport implements ToModel, WithMappedCells
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
     * @param array $row
     */
    public function model(array $row)
    {
    }
    /**
     * TODO：获取数据表注释
     * @return array
     */
    protected function tableMapping()
    {
        $mapping = array();
        $columnsObj  = DB::select(sprintf("SHOW FULL COLUMNS FROM %s", 'os_oauth'));
        foreach ($columnsObj as $columns) {
            array_push($mapping, $columns->Field);
        }
        return $mapping;
    }

    public function mapping(): array
    {
        // TODO: Implement mapping() method.
    }
}

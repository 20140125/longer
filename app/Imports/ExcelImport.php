<?php

namespace App\Imports;

use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithMappedCells;

class ExcelImport implements WithMappedCells, ToModel
{
    /**
     * @return array
     */
    public function mapping(): array
    {
        // TODO: Implement mapping() method.
    }
    /**
     * @param array $row
     *
     * @return Model|Model[]|null
     */
    public function model(array $row)
    {
        // TODO: Implement model() method.
    }
}

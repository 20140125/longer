<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * Class TableComponentController
 * @author <fl140125@gmail.com>
 * @package App\Http\Controllers\Api\v1
 */
class TableComponentController extends BaseController
{
    /**
     * TODO:获取组件数据
     * @return JsonResponse
     */
    public function table()
    {
        $this->validatePost(['table'=>'required|string']);
        $tables = $this->tablesName();
        $data = DB::select(sprintf("SELECT * FROM %s ORDER BY id DESC LIMIT 10",$this->post['table']));
        return $this->ajax_return(Code::SUCCESS,'successfully',
            [
                'data'=>$data,
                'columns'=>$this->tableMapping(),
                'searchOptions' =>  array(
                    //下拉框
                    [
                        'type'    => 'select',
                        'label'   => 'TableName',
                        'prop'    => 'table',
                        'model'   => $this->post['table'],
                        'options' => $tables,
                        'tips'    => 'Please select table name'
                    ],
                    // 文本输入框
                    [
                        'type'    => 'input',
                        'label'   => 'Params',
                        'prop'    => 'input',
                        'model'   => '',
                        'tips'    => 'Please input params'
                    ],
                    //时间日期组件
                    [
                        'type'    => 'datetime',
                        'label'   => 'Date',
                        'prop'    => 'date',
                        'model'   => [],
                        'datetype'  => 'daterange',
                        'format'    => 'yyyy-MM-dd',
                        'tips'    => 'Please input time - Please input time'
                    ]
                ),
                'searchOption' => (Object)['table'=>$this->post['table'],'lan'=>'en']
            ]
        );
    }

    /**
     * TODO:获取数据表名
     * @return array
     */
    protected function tablesName()
    {
        $arr = array();
        $tables = DB::select(sprintf("SHOW TABLES"));
        foreach ($tables as $table) {
            array_push($arr,['label'=>$table->Tables_in_longer,'value'=>$table->Tables_in_longer]);
        }
        return $arr;
    }
    /**
     * TODO：获取数据表注释
     * @return array
     */
    protected function tableMapping()
    {
        $mapping = array();
        $columnsObj  = DB::select(sprintf("SHOW FULL COLUMNS FROM %s", $this->post['table']));
        foreach ($columnsObj as $columns) {
            switch ($this->post['lan']) {
                case 'zh':
                    //字段存在备注的时候
                    if (!empty($columns->Comment)) {
                        array_push($mapping,['label'=>$columns->Comment,'prop'=>$columns->Field]);
                    }
                    //字段不存在备注的时候
                    if (empty($columns->Comment)) {
                        array_push($mapping,['label'=>ucfirst(str_replace('_',' ',$columns->Field)),'prop'=>$columns->Field]);
                    }
                    break;
                default:
                    array_push($mapping,['label'=>ucfirst(str_replace('_',' ',$columns->Field)),'prop'=>$columns->Field]);
                    break;
            }
        }
        return $mapping;
    }
    /**
     * @return JsonResponse
     */
    public function action()
    {
        return $this->ajax_return(Code::SUCCESS,'successfully');
    }
}

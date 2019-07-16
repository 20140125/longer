<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;
use App\Models\Database;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * 数据库管理
 * Class DatabaseController
 * @package App\Http\Controllers\Api\v1
 */
class DatabaseController extends BaseController
{
    /**
     * @var Database $databaseModel 数据表模型
     */
    protected $databaseModel;

    /**
     * DatabaseController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->databaseModel = Database::getInstance();
    }

    /**
     * 数据表列表
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        if ($request->isMethod('get')){
            return $this->ajax_return(Code::METHOD_ERROR,'error');
        }
        $result = $this->databaseModel->lists();
        return $this->ajax_return(Code::SUCCESS,'success',$result);
    }

    /**
     * 数据表备份
     * @param Request $request
     * @return JsonResponse
     */
    public function backup(Request $request)
    {
        if ($request->isMethod('get')){
            return $this->ajax_return(Code::METHOD_ERROR,'error');
        }
        function_exists('set_time_limit') && set_time_limit(0);
        foreach ($this->post['tableName'] as $item){
            $savePath = $this->backupPath.DIRECTORY_SEPARATOR.$item.'.sql';
            $content = $this->databaseModel->createTable($item).$this->databaseModel->sourceTable($item);
            write_file($savePath,$content);
        }
        return $this->ajax_return(Code::SUCCESS,'success');
    }
}

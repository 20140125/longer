<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;
use App\Models\Database;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * 数据库管理
 * Class DatabaseController
 * @author <fl140125@gmail.com>
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
     * TODO:数据表列表
     * @return JsonResponse
     */
    public function index()
    {
        $result = $this->databaseModel->lists();
        return $this->ajax_return(Code::SUCCESS,'successfully',$result);
    }

    /**
     * TODO:数据表备份
     * @return JsonResponse
     */
    public function backup()
    {
        function_exists('set_time_limit') && set_time_limit(0);
        foreach ($this->post['tableName'] as $item){
            $savePath = $this->backupPath.DIRECTORY_SEPARATOR.$item.'.sql';
            $content = $this->databaseModel->createTable($item).$this->databaseModel->sourceTable($item);
            write_file($savePath,$content);
        }
        return $this->ajax_return(Code::SUCCESS,'successfully');
    }
}

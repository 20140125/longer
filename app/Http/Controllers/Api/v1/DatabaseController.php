<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;
use App\Models\Database;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
     * @var string $string
     */
    protected $string;

    /**
     * DatabaseController constructor.
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        parent::__construct($request);
        $this->databaseModel = Database::getInstance();
        $this->string = 'database table '.config('app.database').'.'.($this->post['name'] ?? '');
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
     * @param string name 数据库名称
     * @return JsonResponse
     */
    public function backup()
    {
        $this->validatePost(['name'=>'required|string']);
        function_exists('set_time_limit') && set_time_limit(0);
        $savePath = $this->backupPath.DIRECTORY_SEPARATOR.date('Y_m_d').'_'.get_round_num(6,'number').'_'.$this->post['name'].'_table.sql';
        $content = $this->databaseModel->createTable($this->post['name']).$this->databaseModel->sourceTable($this->post['name']);
        $s_time = time();
        if (write_file($savePath,$content)){
            $e_time = time();
            return $this->ajax_return(Code::SUCCESS,$this->string.' backup successfully',['times'=>$e_time-$s_time]);
        }
        $e_time = time();
        return $this->ajax_return(Code::ERROR,$this->string.' backup failed',['times'=>$e_time-$s_time]);
    }

    /**
     * TODO：修复数据表
     * @param string name 数据库名称
     * @return JsonResponse
     */
    public function repair()
    {
        $this->validatePost(['name'=>'required|string']);
        $result = $this->databaseModel->repairTable($this->post['name']);
        Log::error(json_encode($result));
        return $result ? $this->ajax_return(Code::SUCCESS,$this->string.' repair successfully') : $this->ajax_return(Code::ERROR,$this->string.' repair failed');
    }

    /**
     * TODO：优化数据表
     * @param string name 数据库名称
     * @return JsonResponse
     */
    public function optimize()
    {
        $this->validatePost(['name'=>'required|string']);
        $result = $this->databaseModel->optimizeTable($this->post['name']);
        return $result ? $this->ajax_return(Code::SUCCESS,$this->string.' optimize successfully') : $this->ajax_return(Code::ERROR,$this->string.' optimize failed');
    }
    /**
     * TODO：优化数据表
     * @param string name 数据库名称
     * @param string common 备注
     * @return JsonResponse
     */
    public function comment()
    {
        $this->validatePost(['name'=>'required|string','comment'=>'required|string']);
        $result = $this->databaseModel->commentTable($this->post['name'],$this->post['comment']);
        return is_array($result) ? $this->ajax_return(Code::SUCCESS,$this->string.'  comment update successfully') : $this->ajax_return(Code::ERROR,$this->string.' comment update failed');
    }
}

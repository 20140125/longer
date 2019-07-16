<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * 文件管理
 * Class FileController
 * @package App\Http\Controllers\Api\v1
 */
class FileController extends BaseController
{
    /**
     * 文件列表
     * @param Request $request
     * @return Factory|JsonResponse|View
     */
    public function index(Request $request)
    {
        if ($request->isMethod('get')){
            return $this->ajax_return(Code::METHOD_ERROR,'error');
        }
        $basename = $this->post['basename'];
        $path = $this->post['path'];
        $fileLists = file_lists(file_path($path,$basename));
        return $this->ajax_return(Code::SUCCESS,'success',$fileLists);
    }

    /**
     * 文件打包
     * @param Request $request
     * @return JsonResponse
     */
    public function compression(Request $request)
    {
        if ($request->isMethod('get')){
            return $this->ajax_return(Code::METHOD_ERROR,'error');
        }
        $zipProductPath = $this->backupPath.$this->post['type'].'_'.date("YmdHis").'.zip';
        $bool = gzip($this->post['docLists'],$this->post['path'],$zipProductPath);
        if ($bool){
            return $this->ajax_return(Code::SUCCESS,'compression file success');
        }
        return $this->ajax_return(Code::ERROR,'compression file error');
    }

    /**
     * 文件详情
     * @param Request $request
     * @return JsonResponse
     */
    public function read(Request $request)
    {
        if ($request->isMethod('get')){
            return $this->ajax_return(Code::METHOD_ERROR,'error');
        }
        $validate = Validator::make($this->post,['path'=>'required|string']);
        if ($validate->fails()){
            return $this->ajax_return(Code::ERROR,$validate->errors()->first());
        }
        return $this->ajax_return(Code::SUCCESS,'success',['content'=>open_file($this->post['path'])]);
    }
    /**
     * 文件解压
     * @param Request $request
     * @return JsonResponse
     */
    public function Decompression(Request $request)
    {
        if ($request->isMethod('get')){
            return $this->ajax_return(Code::METHOD_ERROR,'error');
        }
        $filePath = file_path($this->post['path']??'base_path',$this->post['basename']??'/README.md');
        $bool = unzip($filePath,public_path());
        if ($bool){
            return $this->ajax_return(Code::SUCCESS,'Decompression file success');
        }
        return $this->ajax_return(Code::ERROR,'Decompression file error');
    }

    /**
     * 文件上传
     * @param Request $request
     * @return JsonResponse
     */
    public function upload(Request $request)
    {
        if ($request->isMethod('get')){
            return $this->ajax_return(Code::METHOD_ERROR,'error');
        }
        $file = $request->file('file');
        if ($file->isValid()){
            //获取文件的扩展名
            $ext = $file->getClientOriginalExtension();
            //获取文件的绝对路径
            $path = $file->getRealPath();
            //定义文件名
            $filename = date('YmdHis').uniqid().'.'.$ext;
            //存储文件。disk里面的public。总的来说，就是调用disk模块里的public配置
            Storage::disk('public')->put($filename, file_get_contents($path));
            return $this->ajax_return(Code::SUCCESS,'upload success',array('src'=>config('filesystems.disks')['public']['url'].'/'.$filename));
        }
        return $this->ajax_return(Code::ERROR,'upload error',array('src'=>''));
    }

    /**
     * 文件下载
     * @param Request $request
     * @param Response $response
     * @return BinaryFileResponse
     */
    public function download(Request $request,Response $response)
    {
        $filePath = file_path($request->get('path')??'base_path',$request->get('basename')??'/README.md');
        return $response::download($filePath,basename($filePath));
    }

    /**
     * 文件保存
     * @param Request $request
     * @return JsonResponse
     */
    public function save(Request $request)
    {
        if ($request->isMethod('get')){
            return $this->ajax_return(Code::METHOD_ERROR,'error');
        }
        $filePath = file_path($this->post['path']??'base_path',$this->post['basename']??'/README.md');
        write_file($filePath,$this->post['content']);
        return $this->ajax_return(Code::SUCCESS,'file save success');
    }

    /**
     * 文件删除
     * @param Request $request
     * @return JsonResponse
     */
    public function delete(Request $request)
    {
        if ($request->isMethod('get')){
            return $this->ajax_return(Code::METHOD_ERROR,'error');
        }
        $filePath = file_path($this->post['path']??'',$this->post['basename']??'');
        $bool = remove_files($filePath);
        if ($bool){
            return $this->ajax_return(Code::SUCCESS,'delete file success');
        }
        return $this->ajax_return(Code::ERROR,'delete file error');
    }
}

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
     * todo：文件列表
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
        return $this->ajax_return(Code::SUCCESS,'successfully',$fileLists);
    }

    /**
     * todo：文件打包
     * @param Request $request
     * @return JsonResponse
     */
    public function compression(Request $request)
    {
        if ($request->isMethod('get')){
            return $this->ajax_return(Code::METHOD_ERROR,'error');
        }
        $validate = Validator::make($this->post,['path'=>'required|string','resource'=>'required|string','docLists'=>'required|array']);
        if ($validate->fails()){
            return $this->ajax_return(Code::ERROR,$validate->errors()->first());
        }
        $bool = gzip($this->post['docLists'],$this->post['path'],$this->post['resource'].'_'.date("YmdHis").'.zip');
        if ($bool){
            return $this->ajax_return(Code::SUCCESS,'compression file successfully');
        }
        return $this->ajax_return(Code::ERROR,'compression file error');
    }

    /**
     * todo：文件内容
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
        return $this->ajax_return(Code::SUCCESS,'successfully',['content'=>open_file($this->post['path'])]);
    }
    /**
     * todo：文件解压
     * @param Request $request
     * @return JsonResponse
     */
    public function Decompression(Request $request)
    {
        if ($request->isMethod('get')){
            return $this->ajax_return(Code::METHOD_ERROR,'error');
        }
        $validate = Validator::make($this->post,['path'=>'required|string']);
        if ($validate->fails()){
            return $this->ajax_return(Code::ERROR,$validate->errors()->first());
        }
        $bool = unzip($this->post['path'],$this->post['resource']);
        if ($bool){
            //删除压缩包 (个人需求而定)
            remove_files($this->post['path']);
            return $this->ajax_return(Code::SUCCESS,'Decompression file successfully');
        }
        return $this->ajax_return(Code::ERROR,'Decompression file error');
    }

    /**
     * todo：文件上传
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
            return $this->ajax_return(Code::SUCCESS,'upload file successfully',array('src'=>config('filesystems.disks')['public']['url'].'/'.$filename));
        }
        return $this->ajax_return(Code::ERROR,'upload file failed',array('src'=>''));
    }

    /**
     * todo：文件下载
     * @param Request $request
     * @param Response $response
     * @return JsonResponse|BinaryFileResponse
     */
    public function download(Request $request,Response $response)
    {
        if (empty($request->get('path'))){
            return $response::json(array('info'=>'下载文件不存在'),404);
        }
        $username = $this->userModel->getResult('access_token',$request->get('token'));
        if (empty($username)){
            return $this->ajax_return(Code::ERROR,'permission denied');
        }
        return $response::download($request->get('path'),basename($request->get('path')));
    }

    /**
     * todo：文件更新
     * @param Request $request
     * @return JsonResponse
     */
    public function update(Request $request)
    {
        if ($request->isMethod('get')){
            return $this->ajax_return(Code::METHOD_ERROR,'error');
        }
        $validate = Validator::make($this->post,['path'=>'required|string']);
        if ($validate->fails()){
            return $this->ajax_return(Code::ERROR,$validate->errors()->first());
        }
        write_file($this->post['path'],$this->post['content']);
        return $this->ajax_return(Code::SUCCESS,'file save successfully');
    }

    /**
     * todo：文件删除
     * @param Request $request
     * @return JsonResponse
     */
    public function delete(Request $request)
    {
        if ($request->isMethod('get')){
            return $this->ajax_return(Code::METHOD_ERROR,'error');
        }
        $validate = Validator::make($this->post,['path'=>'required|string']);
        if ($validate->fails()){
            return $this->ajax_return(Code::ERROR,$validate->errors()->first());
        }
        //服务器上面需要 777 权限才能删除文件
        chmod($this->post['path'],0777);
        $bool = remove_files($this->post['path']);
        if ($bool){
            return $this->ajax_return(Code::SUCCESS,'remove file successfully');
        }
        return $this->ajax_return(Code::ERROR,'remove file failed');
    }

    /**
     * todo：文件新建
     * @param Request $request
     * @return JsonResponse
     */
    public function save(Request $request)
    {
        if ($request->isMethod('get')){
            return $this->ajax_return(Code::METHOD_ERROR,'error');
        }
        $validate = Validator::make($this->post,['path'=>'required|string']);
        if ($validate->fails()){
            return $this->ajax_return(Code::ERROR,$validate->errors()->first());
        }
        $bool = save_file($this->post['path']);
        if ($bool){
            return $this->ajax_return(Code::SUCCESS,'create file successfully');
        }
        return $this->ajax_return(Code::ERROR,'create file failed');
    }

    /**
     * todo：文件重命名
     * @param Request $request
     * @return JsonResponse
     */
    public function rename(Request $request)
    {
        if ($request->isMethod('get')){
            return $this->ajax_return(Code::METHOD_ERROR,'error');
        }
        $validate = Validator::make($this->post,['oldFile'=>'required|string','newFile'=>'required|string']);
        if ($validate->fails()){
            return $this->ajax_return(Code::ERROR,$validate->errors()->first());
        }
        $bool = file_rename($this->post['oldFile'],$this->post['newFile']);
        if ($bool){
            return $this->ajax_return(Code::SUCCESS,'rename file successfully');
        }
        return $this->ajax_return(Code::ERROR,'rename file failed');
    }

    /**
     * todo：修改文件权限
     * @param Request $request
     * @return JsonResponse
     */
    public function auth(Request $request)
    {
        if ($request->isMethod('get')){
            return $this->ajax_return(Code::METHOD_ERROR,'error');
        }
        $validate = Validator::make($this->post,['path'=>'required|string','auth'=>'required|integer|max:666']);
        if ($validate->fails()){
            return $this->ajax_return(Code::ERROR,$validate->errors()->first());
        }
        chmod($this->post['path'],octdec((int)"0".$this->post['auth']));
        if (file_chmod($this->post['path'])==$this->post['auth']){
            return $this->ajax_return(Code::SUCCESS,'Modify file permissions successfully');
        }
        return $this->ajax_return(Code::ERROR,'Modify file permissions failed');
    }
}

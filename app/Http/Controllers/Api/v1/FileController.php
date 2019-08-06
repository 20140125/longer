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
 * todo：save文件管理
 * Class FileController
 * @author <fl140125@gmail.com>
 * @package App\Http\Controllers\Api\v1
 */
class FileController extends BaseController
{
    /**
     * todo：文件列表
     * @return Factory|JsonResponse|View
     */
    public function index()
    {
        $this->validatePost(['path'=>'required|string','basename'=>'required|string']);
        $fileLists = file_lists(file_path($this->post['path'],$this->post['basename']));
        return $this->ajax_return(Code::SUCCESS,'successfully',$fileLists);
    }

    /**
     * todo：文件打包
     * @return JsonResponse
     */
    public function compression()
    {
        $this->validatePost(['path'=>'required|string','resource'=>'required|string','docLists'=>'required|array']);
        $bool = gzip($this->post['docLists'],$this->post['path'],$this->post['resource'].'_'.date("YmdHis").'.zip');
        if ($bool){
            return $this->ajax_return(Code::SUCCESS,'compression file successfully');
        }
        return $this->ajax_return(Code::ERROR,'compression file failed');
    }

    /**
     * todo：文件内容
     * @return JsonResponse
     */
    public function read()
    {
        $this->validatePost(['path'=>'required|string']);
        return $this->ajax_return(Code::SUCCESS,'successfully',['content'=>open_file($this->post['path'])]);
    }
    /**
     * todo：文件解压
     * @return JsonResponse
     */
    public function Decompression()
    {
        $this->validatePost(['path'=>'required|string']);
        $bool = unzip($this->post['path'],$this->post['resource']);
        if ($bool){
            //删除压缩包 (个人需求而定)
            remove_files($this->post['path']);
            return $this->ajax_return(Code::SUCCESS,'Decompression file successfully');
        }
        return $this->ajax_return(Code::ERROR,'Decompression file failed');
    }

    /**
     * todo：文件上传
     * @param Request $request
     * @return JsonResponse
     */
    public function upload(Request $request)
    {
        $file = $request->file('file');
        if ($file->isValid()) {
            //获取文件的扩展名
            $ext = $file->getClientOriginalExtension();
            //获取文件的绝对路径
            $path = $file->getRealPath();
            //修改用户图片
            if (!empty($this->post['rand']) && $this->post['rand']) {
                //图片格式上传错误
                if (!in_array($ext,['jpg','png','jpeg','gif'])) {
                    return $this->ajax_return(Code::ERROR,'upload image format error');
                }
                //图片大小上传错误
                if ($file->getSize()>2*1024*1024) {
                    return $this->ajax_return(Code::ERROR,'upload image size error');
                }
                $filename = md5(date('YmdHis')).uniqid().".".$ext;
                Storage::disk('public')->put($filename, file_get_contents($path));
                return $this->ajax_return(Code::SUCCESS,'upload file successfully',array('src'=>config('app.url').'storage/'.$filename));
            }
            //文件管理
            $this->validatePost(['path'=>'required|string']);
            //获取文件名
            $filename = $file->getClientOriginalName();
            //存储文件。disk里面的public。总的来说，就是调用disk模块里的public配置
            Storage::disk('public')->put($filename, file_get_contents($path));
            //文件移动
            Storage::disk('public')->move($this->post['path'].DIRECTORY_SEPARATOR,$filename);
            //删除文件
            Storage::disk('public')->delete($filename);
            return $this->ajax_return(Code::SUCCESS,'upload file successfully',array('name'=>$filename));
        }
        return $this->ajax_return(Code::ERROR,'upload file failed');
    }

    /**
     * todo：文件下载
     * @param Request $request
     * @param Response $response
     * @return JsonResponse|BinaryFileResponse
     */
    public function download(Request $request,Response $response)
    {
        if ($request->isMethod('get')){
            return $this->ajax_return(Code::METHOD_ERROR,'error');
        }
        $username = $this->userModel->getResult('access_token',$request->get('token'));
        if (empty($username)){
            return $this->ajax_return(Code::ERROR,'permission denied');
        }
        return $response::download($request->get('path'),basename($request->get('path')));
    }

    /**
     * todo：文件更新
     * @return JsonResponse
     */
    public function update()
    {
        $this->validatePost(['path'=>'required|string']);
        write_file($this->post['path'],$this->post['content']??'');
        return $this->ajax_return(Code::SUCCESS,'file save successfully');
    }

    /**
     * todo：文件删除
     * @return JsonResponse
     */
    public function delete()
    {
        $this->validatePost(['path'=>'required|string']);
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
     * @return JsonResponse
     */
    public function save()
    {
        $this->validatePost(['path'=>'required|string']);
        $bool = save_file($this->post['path']);
        if ($bool){
            return $this->ajax_return(Code::SUCCESS,'create file successfully');
        }
        return $this->ajax_return(Code::ERROR,'create file failed');
    }

    /**
     * todo：文件重命名
     * @return JsonResponse
     */
    public function rename()
    {
        $this->validatePost(['oldFile'=>'required|string','newFile'=>'required|string']);
        $bool = file_rename($this->post['oldFile'],$this->post['newFile']);
        if ($bool){
            return $this->ajax_return(Code::SUCCESS,'rename file successfully');
        }
        return $this->ajax_return(Code::ERROR,'rename file failed');
    }

    /**
     * todo：修改文件权限
     * @return JsonResponse
     */
    public function auth()
    {
        $this->validatePost(['path'=>'required|string','auth'=>'required|integer|max:666']);
        chmod($this->post['path'],octdec((int)"0".$this->post['auth']));
        if (file_chmod($this->post['path'])==$this->post['auth']){
            return $this->ajax_return(Code::SUCCESS,'Modify file permissions successfully');
        }
        return $this->ajax_return(Code::ERROR,'Modify file permissions failed');
    }

    /**
     * todo：图片预览
     * @return JsonResponse
     */
    public function preview()
    {
        $this->validatePost(['name'=>'required|string']);
        $url = config('app.url').DIRECTORY_SEPARATOR.'storage/'.$this->post['name'];
        return $this->ajax_return(Code::SUCCESS,'Get the file address successfully', ['src'=>$url]);
    }
}

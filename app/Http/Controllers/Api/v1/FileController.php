<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * TODO:：save文件管理
 * Class FileController
 * @author <fl140125@gmail.com>
 * @package App\Http\Controllers\Api\v1
 */
class FileController extends BaseController
{
    /**
     * TODO:：文件列表
     * @param string path [base_path,storage_path,public_path]
     * @param string basename
     * @return Factory|JsonResponse|View
     */
    public function index()
    {
        $this->validatePost(['path'=>'required|string','basename'=>'required|string']);
        $fileLists = file_lists(file_path($this->post['path'],$this->post['basename']));
        return $this->ajax_return(Code::SUCCESS,'successfully',$fileLists);
    }

    /**
     * TODO:：文件打包
     * @param string path 文件路径
     * @param string resource 打包后文件前缀
     * @param array docLists 资源列表
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
     * TODO:：文件内容
     * @param string path 文件路径
     * @return JsonResponse
     */
    public function read()
    {
        $this->validatePost(['path'=>'required|string']);
        return $this->ajax_return(Code::SUCCESS,'successfully',['content'=>open_file($this->post['path'])]);
    }
    /**
     * TODO:：文件解压
     * @param string path 文件路径
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
     * TODO:：文件上传
     * @param Request $request (file:文件资源,rand:是否随机,path:文件路径)
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
                switch (strtolower($ext)) {
                    case 'jpg':
                    case 'gif':
                    case 'png':
                    case 'jpeg':
                        //图片大小上传错误
                        if ($file->getSize()>2*1024*1024) {
                            return $this->ajax_return(Code::ERROR,'upload image size error');
                        }
                        break;
                    case 'mp4':
                        if ($file->getSize()>5*1024*1024) {
                            return $this->ajax_return(Code::ERROR,'upload video size error');
                        }
                        break;
                    default:
                        return $this->ajax_return(Code::ERROR,'Unsupported file format');
                }
                $filename = date('Ymd')."/".md5(date('YmdHis')).uniqid().".".$ext;
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
            $file->move($this->post['path'],$filename);
            //删除文件
            Storage::disk('public')->delete($filename);
            return $this->ajax_return(Code::SUCCESS,'upload file successfully',array('name'=>$filename));
        }
        return $this->ajax_return(Code::ERROR,'upload file failed');
    }

    /**
     * TODO:：文件下载
     * @param Request $request （token:用户标识，path:文件路径）
     * @param Response $response
     * @return JsonResponse|BinaryFileResponse
     */
    public function download(Request $request,Response $response)
    {
        $username = $this->userModel->getResult('remember_token',$request->get('token'));
        if (empty($username)){
            return $this->ajax_return(Code::NOT_FOUND,'permission denied');
        }
        if (is_file($request->get('path'))){
            return $response::download($request->get('path'),basename($request->get('path')));
        }
        return $this->ajax_return(Code::NOT_FOUND,'permission denied');
    }

    /**
     * TODO:：文件更新
     * @param string path 文件路径
     * @return JsonResponse
     */
    public function update()
    {
        $this->validatePost(['path'=>'required|string']);
        write_file($this->post['path'],$this->post['content']??'');
        return $this->ajax_return(Code::SUCCESS,'file save successfully');
    }

    /**
     * TODO:：文件删除
     * @param string path 文件路径
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
     * TODO:：文件新建
     * @param string path 文件路径
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
     * TODO:：文件重命名
     * @param string oldFile 文件旧名称
     * @param string newFile 文件新名称
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
     * TODO:：修改文件权限
     * @param string path 文件路径
     * @param integer auth 权限值
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
     * TODO:：图片预览
     * @param string name 文件名称
     * @return JsonResponse
     */
    public function preview()
    {
        $this->validatePost(['name'=>'required|string']);
        $url = config('app.url').DIRECTORY_SEPARATOR.'storage/'.$this->post['name'];
        return $this->ajax_return(Code::SUCCESS,'Get the file address successfully', ['src'=>$url]);
    }
}

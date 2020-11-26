<?php
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Utils\Code;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

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
        $this->validatePost(
            [
                'path'=>'required|string',
                'basename'=>'required|string',
                'sort'=>'required|string|in:type,time',
                'sort_order'=>'required|string|in:asc,desc'
            ]
        );
        $fileLists = getFileLists(
            getFilePath($this->post['path'], $this->post['basename']),
            [],
            $this->post['sort'] ?? 'type',
            ($this->post['sort_order'] === 'desc') ? SORT_DESC : SORT_ASC
        );
        return $this->ajaxReturn(Code::SUCCESS, 'successfully', $fileLists);
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
        if (!is_dir($this->post['path'])) {
            return $this->ajaxReturn(Code::ERROR, 'file does not exist');
        }
        $bool = gzipFile(
            $this->post['docLists'],
            $this->post['path'],
            $this->post['resource'].'_'.date("YmdHis").'.zip'
        );
        return $bool ?  $this->ajaxReturn(Code::SUCCESS, 'compression file successfully')
            : $this->ajaxReturn(Code::ERROR, 'compression file failed');
    }

    /**
     * TODO:：文件内容
     * @param string path 文件路径
     * @return JsonResponse
     */
    public function read()
    {
        $this->validatePost(['path'=>'required|string']);
        if (!file_exists($this->post['path'])) {
            return $this->ajaxReturn(Code::ERROR, 'file does not exist');
        }
        if (filesize($this->post['path'])>1024*1024*3) {
            return $this->ajaxReturn(Code::ERROR, 'File size exceeds limit');
        }
        return $this->ajaxReturn(Code::SUCCESS, 'Get file successfully', ['content'=>openFile($this->post['path'])]);
    }
    /**
     * TODO:：文件解压
     * @param string path 文件路径
     * @return JsonResponse
     */
    public function decompression()
    {
        $this->validatePost(['path'=>'required|string']);
        if (!file_exists($this->post['path'])) {
            return $this->ajaxReturn(Code::ERROR, 'file does not exist');
        }
        $bool = unzipFile($this->post['path'], $this->post['resource'], true);
        return $bool ? $this->ajaxReturn(Code::SUCCESS, 'Decompression file successfully')
            : $this->ajaxReturn(Code::ERROR, 'Decompression file failed');
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
            $info = array(
                'username' => $this->users->username,
                'href' => '/v1/file/upload',
                'msg' => 'upload file '.$file->getClientOriginalName().' successfully');
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
                            return $this->ajaxReturn(Code::ERROR, 'upload image size error');
                        }
                        break;
                    case 'mp4':
                        if ($file->getSize()>5*1024*1024) {
                            return $this->ajaxReturn(Code::ERROR, 'upload video size error');
                        }
                        break;
                    default:
                        return $this->ajaxReturn(Code::ERROR, 'Unsupported file format');
                }
                $filename = date('Ymd')."/".md5(date('YmdHis')).uniqid().".".$ext;
                Storage::disk('public')->put($filename, file_get_contents($path));
                actLog($info);
                return $this->ajaxReturn(
                    Code::SUCCESS,
                    'upload file successfully',
                    array('src'=>config('app.url').'storage/'.$filename)
                );
            }
            //文件管理
            $this->validatePost(['path'=>'required|string']);
            //获取文件名
            $filename = $file->getClientOriginalName();
            //文件移动
            $file->move($this->post['path'], $filename);
            actLog($info);
            return $this->ajaxReturn(Code::SUCCESS, 'upload file successfully', array('name'=>$filename));
        }
        return $this->ajaxReturn(Code::ERROR, 'upload file failed');
    }

    /**
     * TODO:：文件更新
     * @param string path 文件路径
     * @return JsonResponse
     */
    public function update()
    {
        $this->validatePost(['path'=>'required|string']);
        if (!file_exists($this->post['path'])) {
            return $this->ajaxReturn(Code::ERROR, 'file does not exist');
        }
        writeFile($this->post['path'], $this->post['content'] ?? '');
        return $this->ajaxReturn(Code::SUCCESS, 'file save successfully');
    }

    /**
     * TODO:：文件删除
     * @param string path 文件路径
     * @return JsonResponse
     */
    public function delete()
    {
        $this->validatePost(['path'=>'required|string']);
        if (!file_exists($this->post['path'])) {
            return $this->ajaxReturn(Code::ERROR, 'file does not exist');
        }
        //服务器上面需要 777 权限才能删除文件
        chmod($this->post['path'], 0777);
        $bool = removeFiles($this->post['path']);
        return $bool ? $this->ajaxReturn(Code::SUCCESS, 'remove file successfully')
            : $this->ajaxReturn(Code::ERROR, 'remove file failed');
    }

    /**
     * TODO:：文件新建
     * @param string path 文件路径
     * @return JsonResponse
     */
    public function save()
    {
        $this->validatePost(['path'=>'required|string']);
        if (!file_exists($this->post['path'])) {
            return $this->ajaxReturn(Code::ERROR, 'file does not exist');
        }
        $bool = saveFile($this->post['path']);
        return $bool ? $this->ajaxReturn(Code::SUCCESS, 'create file successfully')
            : $this->ajaxReturn(Code::ERROR, 'create file failed');
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
        $bool = renameFile($this->post['oldFile'], $this->post['newFile']);
        return $bool ? $this->ajaxReturn(Code::SUCCESS, 'rename file successfully')
            : $this->ajaxReturn(Code::ERROR, 'rename file failed');
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
        if (!file_exists($this->post['path'])) {
            return $this->ajaxReturn(Code::ERROR, 'file does not exist');
        }
        chmod($this->post['path'], octdec((int)"0".$this->post['auth']));
        $bool = chmodFile($this->post['path']) == $this->post['auth'];
        return $bool ? $this->ajaxReturn(Code::SUCCESS, 'Modify file permissions successfully')
            : $this->ajaxReturn(Code::ERROR, 'Modify file permissions failed');
    }
}

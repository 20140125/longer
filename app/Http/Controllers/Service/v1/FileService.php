<?php

namespace App\Http\Controllers\Service\v1;

use App\Http\Controllers\Utils\Code;
use Illuminate\Support\Facades\Storage;

class FileService extends BaseService
{
    /**
     * @var static $instance
     */
    private static $instance;

    /**
     * @return static
     */
    public static function getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * 获取文件列表
     * @param $form
     * @param array $permission
     * @return array
     */
    public function getFileLists($form, array $permission = [])
    {
        $this->return['lists'] = getFileLists(getFilePath($form['path'], $form['basename']), $permission);
        return $this->return;
    }

    /**
     * todo：获取文件内容
     * @param $form
     * @return array|string
     */
    public function readFile($form)
    {
        if (!file_exists($form['path'])) {
            $this->return['code'] = Code::ERROR;
            $this->return['message'] = 'File does not exist';
        }
        if (filesize($form['path']) > 1024 * 1024 * 3) {
            $this->return['code'] = Code::ERROR;
            $this->return['message'] = 'File size exceeds limit';
        }
        $result = getFileContent($form['path']);
        !empty($result['code']) ? $this->return = $result : $this->return['lists'] = $result;
        return $this->return;
    }

    /**
     * 写入文件内容
     * @param $form
     * @return array|int
     */
    public function updateFile($form)
    {
        if (!file_exists($form['path'])) {
            $this->return['code'] = Code::ERROR;
            $this->return['message'] = 'File does not exist';
        }
        $result = writeFile($form['path'], $form['content']);
        !empty($result['code']) ? $this->return = $result : $this->return['lists'] = $form;
        $this->return['message'] = !empty($result['code']) ? $this->return['message'] = $result['message'] : 'update file successfully';
        return $this->return;
    }

    /**
     * 文件打包
     * @param $form
     * @return array|bool
     */
    public function gZipFile($form)
    {
        if (!file_exists($form['path'])) {
            $this->return['code'] = Code::ERROR;
            $this->return['message'] = 'File does not exist';
        }
        $result = gZipFile($form['docLists'], $form['path'], $form['resource'] . '_' . date('YmdHis') . '.zip');
        !empty($result['code']) ? $this->return = $result : $this->return['lists'] = $form;
        return $this->return;
    }

    /**
     * 文件解压
     * @param $form
     * @return array|bool
     */
    public function unGZipFile($form)
    {
        if (!file_exists($form['path'])) {
            $this->return['code'] = Code::ERROR;
            $this->return['message'] = 'File does not exist';
        }
        $result = unGZipFile($form['path'], $form['resource'], false);
        !empty($result['code']) ? $this->return = $result : $this->return['lists'] = $form;
        return $this->return;
    }

    /**
     * 文件删除
     * @param $form
     * @return array|bool
     */
    public function removeFile($form)
    {
        if (!file_exists($form['path'])) {
            $this->return['code'] = Code::ERROR;
            $this->return['message'] = 'File does not exist';
        }
        chmod($form['path'], 0777);
        $result = removeFiles($form['path']);
        !empty($result['code']) ? $this->return = $result : $this->return['lists'] = $form;
        return $this->return;
    }

    /**
     * 文件添加
     * @param $form
     * @return array|bool
     */
    public function createFile($form)
    {
        $result = createFile($form['path']);
        !empty($result['code']) ? $this->return = $result : $this->return['lists'] = $form;
        return $this->return;
    }

    /**
     * 设置文件权限
     * @param $form
     * @return array|bool
     */
    public function setFileAuth($form)
    {
        if (!file_exists($form['path'])) {
            $this->return['code'] = Code::ERROR;
            $this->return['message'] = 'File does not exist';
        }
        chmod($form['path'], octdec((int)'0' . $form['auth']));
        $result = chmodFile($form['path']) == $form['auth'];
        !empty($result['code']) ? $this->return = $result : $this->return['lists'] = $form;
        return $this->return;
    }

    /**
     * 文件重命名
     * @param $form
     * @return array|bool
     */
    public function renameFile($form)
    {
        $result = renameFile($form['oldFile'], $form['newFile']);
        !empty($result['code']) ? $this->return = $result : $this->return['lists'] = $form;
        return $this->return;
    }

    /**
     * ：文件上传
     * @param $request
     * @param $form
     * @return array
     */
    public function uploadFile($request, $form)
    {
        $file = $request->file('file');
        if (!$file->isValid()) {
            $this->return['code'] = Code::ERROR;
            $this->return['message'] = 'upload file failed';
            return $this->return;
        }
        /* 获取文件的扩展名 */
        $ext = $file->getClientOriginalExtension();
        /* 获取文件的绝对路径 */
        $path = $file->getRealPath();
        $imgExt = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array(strtolower($ext), $imgExt)) {
            /* 图片大小上传错误 */
            if ($file->getSize() > 2 * 1024 * 1024) {
                $this->return['code'] = Code::ERROR;
                $this->return['message'] = 'upload image size error';
                return $this->return;
            }
        }
        /* 视频大小校验 */
        if (strtolower($ext) == 'mp4') {
            if ($file->getSize() > 5 * 1024 * 1024) {
                $this->return['code'] = Code::ERROR;
                $this->return['message'] = 'upload video size error';
                return $this->return;
            }
        }
        $imgExt[] = 'mp4';
        /* todo：只允许上传图片和视频（根据个人情况修改） */
        if (!in_array(strtolower($ext), $imgExt)) {
            $this->return['code'] = Code::ERROR;
            $this->return['message'] = 'Unsupported file format';
            return $this->return;
        }
        /* todo：文件名称随机 */
        if (in_array(strtolower($ext), $imgExt) && !empty($form['round_name'])) {
            $filename = date('Ymd') . '/' . time() . '.' . $ext;
            Storage::disk('public')->put($filename, file_get_contents($path));
            $this->return['code'] = Code::SUCCESS;
            $this->return['message'] = 'upload file successfully';
            $this->return['lists'] = array('src' => config('app.url') . 'storage/' . $filename, 'file_type' => $form['file_type'] ?? '');
            return $this->return;
        }
        /* todo：覆盖上传文件名称 */
        $imgExt[] = 'php';
        if (in_array(strtolower($ext), $imgExt) && empty($form['round_name'])) {
            /* 获取文件名 */
            $filename = $file->getClientOriginalName();
            /* 文件移动 */
            $file->move($form['path'], $filename);
            $this->return['code'] = Code::SUCCESS;
            $this->return['message'] = 'upload file successfully';
            $this->return['lists'] = array('src' => config('app.url') . 'storage/' . $filename, 'name' => $filename);
            return $this->return;
        }
        return $this->return;
    }
}

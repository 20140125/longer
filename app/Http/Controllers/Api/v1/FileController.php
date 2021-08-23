<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FileController extends BaseController
{
    /**
     * todo:获取文件列表
     * @return JsonResponse
     */
    public function getFileLists()
    {
        validatePost($this->post, ['path' => 'required|string', 'basename' => 'required|string']);
        $result = $this->fileService->getFileLists($this->post, ['rebar3']);
        return ajaxReturn($result);
    }

    /**
     * todo:读取文件内容
     * @return JsonResponse
     */
    public function readFile()
    {
        validatePost($this->post, ['path' => 'required|string']);
        $result = $this->fileService->readFile($this->post);
        return ajaxReturn($result);
    }

    /**
     * todo:文件更新
     * @return JsonResponse
     */
    public function updateFile()
    {
        validatePost($this->post, ['path' => 'required|string', 'content' => 'required|string']);
        $result = $this->fileService->updateFile($this->post);
        return ajaxReturn($result);
    }

    /**
     * TODO:：文件打包
     * @param string path 文件路径
     * @param string resource 打包后文件前缀
     * @param array docLists 资源列表
     * @return JsonResponse
     */
    public function gZipFile()
    {
        validatePost($this->post, ['path' => 'required|string', 'resource' => 'required|string', 'docLists' => 'required|array']);
        $result = $this->fileService->gZipFile($this->post);
        return ajaxReturn($result);
    }

    /**
     * TODO:：文件解压
     * @param string path 文件路径
     * @param string resource 解压文件名称
     * @return JsonResponse
     */
    public function unGZipFile()
    {
        validatePost($this->post, ['path' => 'required|string', 'resource' => 'required|string']);
        $result = $this->fileService->unGZipFile($this->post);
        return ajaxReturn($result);
    }

    /**
     * TODO:：文件删除
     * @param string path 文件路径
     * @return JsonResponse
     */
    public function removeFile()
    {
        validatePost($this->post, ['path' => 'required|string']);
        $result = $this->fileService->removeFile($this->post);
        return ajaxReturn($result);
    }

    /**
     * TODO:：文件新建
     * @param string path 文件路径
     * @return JsonResponse
     */
    public function createFile()
    {
        validatePost($this->post, ['path' => 'required|string']);
        $result = $this->fileService->createFile($this->post);
        return ajaxReturn($result);
    }

    /**
     * todo:图片上传
     * @param Request $request
     * @return JsonResponse
     */
    public function uploadFile(Request $request)
    {
        $result = $this->fileService->uploadFile($request, $this->post);
        return ajaxReturn($result);
    }

    /**
     * todo:设置文件权限
     * @return JsonResponse
     */
    public function setFileAuth()
    {
        validatePost($this->post, ['path' => 'required|string', 'auth' => 'required|integer|max:666']);
        $result = $this->fileService->setFileAuth($this->post);
        return ajaxReturn($result);
    }

    /**
     * todo:文件重命名
     * @return JsonResponse
     */
    public function renameFile()
    {
        validatePost($this->post, ['oldFile' => 'required|string', 'newFile' => 'required|string']);
        $result = $this->fileService->renameFile($this->post);
        return ajaxReturn($result);
    }
}

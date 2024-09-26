<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FileController extends BaseController
{
    /**
     * 获取文件列表
     * @return JsonResponse
     */
    public function getFileLists()
    {
        validatePost($this->post, ['path' => 'required|string', 'basename' => 'required|string']);
        $result = $this->fileService->getFileLists($this->post, ['rebar3']);
        return ajaxReturn($result);
    }

    /**
     * 读取文件内容
     * @return JsonResponse
     */
    public function readFile()
    {
        validatePost($this->post, ['path' => 'required|string']);
        $result = $this->fileService->readFile($this->post);
        return ajaxReturn($result);
    }

    /**
     * 文件更新
     * @return JsonResponse
     */
    public function updateFile()
    {
        validatePost($this->post, ['path' => 'required|string', 'content' => 'required|string']);
        $result = $this->fileService->updateFile($this->post);
        return ajaxReturn($result);
    }

    /**
     * 文件打包
     * @return JsonResponse
     */
    public function gZipFile()
    {
        validatePost($this->post, ['path' => 'required|string', 'resource' => 'required|string', 'docLists' => 'required|array']);
        $result = $this->fileService->gZipFile($this->post);
        return ajaxReturn($result);
    }

    /**
     * 文件解压
     * @return JsonResponse
     */
    public function unGZipFile()
    {
        validatePost($this->post, ['path' => 'required|string', 'resource' => 'required|string']);
        $result = $this->fileService->unGZipFile($this->post);
        return ajaxReturn($result);
    }

    /**
     * 文件删除
     * @return JsonResponse
     */
    public function removeFile()
    {
        validatePost($this->post, ['path' => 'required|string']);
        $result = $this->fileService->removeFile($this->post);
        return ajaxReturn($result);
    }

    /**
     * 文件新建
     * @return JsonResponse
     */
    public function createFile()
    {
        validatePost($this->post, ['path' => 'required|string']);
        $result = $this->fileService->createFile($this->post);
        return ajaxReturn($result);
    }

    /**
     * 图片上传
     * @param Request $request
     * @return JsonResponse
     */
    public function uploadFile(Request $request)
    {
        $result = $this->fileService->uploadFile($request, $this->post);
        return ajaxReturn($result);
    }

    /**
     * 设置文件权限
     * @return JsonResponse
     */
    public function setFileAuth()
    {
        validatePost($this->post, ['path' => 'required|string', 'auth' => 'required|integer|max:666']);
        $result = $this->fileService->setFileAuth($this->post);
        return ajaxReturn($result);
    }

    /**
     * 文件重命名
     * @return JsonResponse
     */
    public function renameFile()
    {
        validatePost($this->post, ['oldFile' => 'required|string', 'newFile' => 'required|string']);
        $result = $this->fileService->renameFile($this->post);
        return ajaxReturn($result);
    }
}

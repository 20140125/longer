<?php

namespace App\Http\Controllers\Api\v1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FileController extends BaseController
{
    /**
     * 获取文件列表
     * @param Request $request
     * @return JsonResponse
     */
    public function getFileLists(Request $request): JsonResponse
    {
        validatePost($request->get('item'), $this->post, ['path' => 'required|string', 'basename' => 'required|string']);
        $permission = $this->fileService->getConfiguration('DefaultPermissionFile', 'CommonPermission');
        $result = $this->fileService->getFileLists($this->post, $permission);
        return ajaxReturn($result);
    }

    /**
     * 读取文件内容
     * @param Request $request
     * @return JsonResponse
     */
    public function readFile(Request $request): JsonResponse
    {
        validatePost($request->get('item'), $this->post, ['path' => 'required|string']);
        $result = $this->fileService->readFile($this->post);
        return ajaxReturn($result);
    }

    /**
     * 文件更新
     * @param Request $request
     * @return JsonResponse
     */
    public function updateFile(Request $request): JsonResponse
    {
        validatePost($request->get('item'), $this->post, ['path' => 'required|string']);
        $result = $this->fileService->updateFile($this->post);
        return ajaxReturn($result);
    }

    /**
     * ：文件打包
     * @param Request $request
     * @return JsonResponse
     */
    public function gZipFile(Request $request): JsonResponse
    {
        validatePost($request->get('item'), $this->post, ['path' => 'required|string', 'resource' => 'required|string', 'docLists' => 'required|array']);
        $result = $this->fileService->gZipFile($this->post);
        return ajaxReturn($result);
    }

    /**
     * ：文件解压
     * @param Request $request
     * @return JsonResponse
     */
    public function unGZipFile(Request $request): JsonResponse
    {
        validatePost($request->get('item'), $this->post, ['path' => 'required|string', 'resource' => 'required|string']);
        $result = $this->fileService->unGZipFile($this->post);
        return ajaxReturn($result);
    }

    /**
     * ：文件删除
     * @param Request $request
     * @return JsonResponse
     */
    public function removeFile(Request $request): JsonResponse
    {
        validatePost($request->get('item'), $this->post, ['path' => 'required|string']);
        $result = $this->fileService->removeFile($this->post);
        return ajaxReturn($result);
    }

    /**
     * ：文件新建
     * @param Request $request
     * @return JsonResponse
     */
    public function createFile(Request $request): JsonResponse
    {
        validatePost($request->get('item'), $this->post, ['path' => 'required|string']);
        $result = $this->fileService->createFile($this->post);
        return ajaxReturn($result);
    }

    /**
     * 图片上传
     * @param Request $request
     * @return JsonResponse
     */
    public function uploadFile(Request $request): JsonResponse
    {
        validatePost($request->get('item'));
        $result = $this->fileService->uploadFile($request, $this->post);
        return ajaxReturn($result);
    }

    /**
     * 设置文件权限
     * @param Request $request
     * @return JsonResponse
     */
    public function setFileAuth(Request $request): JsonResponse
    {
        validatePost($request->get('item'), $this->post, ['path' => 'required|string', 'auth' => 'required|integer|max:666']);
        $result = $this->fileService->setFileAuth($this->post);
        return ajaxReturn($result);
    }

    /**
     * 文件重命名
     * @param Request $request
     * @return JsonResponse
     */
    public function renameFile(Request $request): JsonResponse
    {
        validatePost($request->get('item'), $this->post, ['oldFile' => 'required|string', 'newFile' => 'required|string']);
        $result = $this->fileService->renameFile($this->post);
        return ajaxReturn($result);
    }
}

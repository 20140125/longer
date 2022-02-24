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
    public function getFileLists(Request $request)
    {
        validatePost($request->get('item'), $this->post, ['path' => 'required|string', 'basename' => 'required|string']);
        $permission = $this->fileService->getConfiguration('DefaultPermissionFile', 'CommonPermission');
        $result = $this->fileService->getFileLists($this->post, $permission);
        return ajaxReturn($result);
    }

    /**
     * todo:读取文件内容
     * @return JsonResponse
     */
    public function readFile(Request $request)
    {
        validatePost($request->get('item'), $this->post, ['path' => 'required|string']);
        $result = $this->fileService->readFile($this->post);
        return ajaxReturn($result);
    }

    /**
     * todo:文件更新
     * @return JsonResponse
     */
    public function updateFile(Request $request)
    {
        validatePost($request->get('item'), $this->post, ['path' => 'required|string']);
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
    public function gZipFile(Request $request)
    {
        validatePost($request->get('item'), $this->post, ['path' => 'required|string', 'resource' => 'required|string', 'docLists' => 'required|array']);
        $result = $this->fileService->gZipFile($this->post);
        return ajaxReturn($result);
    }

    /**
     * TODO:：文件解压
     * @param string path 文件路径
     * @param string resource 解压文件名称
     * @return JsonResponse
     */
    public function unGZipFile(Request $request)
    {
        validatePost($request->get('item'), $this->post, ['path' => 'required|string', 'resource' => 'required|string']);
        $result = $this->fileService->unGZipFile($this->post);
        return ajaxReturn($result);
    }

    /**
     * TODO:：文件删除
     * @param string path 文件路径
     * @return JsonResponse
     */
    public function removeFile(Request $request)
    {
        validatePost($request->get('item'), $this->post, ['path' => 'required|string']);
        $result = $this->fileService->removeFile($this->post);
        return ajaxReturn($result);
    }

    /**
     * TODO:：文件新建
     * @param string path 文件路径
     * @return JsonResponse
     */
    public function createFile(Request $request)
    {
        validatePost($request->get('item'), $this->post, ['path' => 'required|string']);
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
        validatePost($request->get('item'));
        $result = $this->fileService->uploadFile($request, $this->post);
        return ajaxReturn($result);
    }

    /**
     * todo:设置文件权限
     * @return JsonResponse
     */
    public function setFileAuth(Request $request)
    {
        validatePost($request->get('item'), $this->post, ['path' => 'required|string', 'auth' => 'required|integer|max:666']);
        $result = $this->fileService->setFileAuth($this->post);
        return ajaxReturn($result);
    }

    /**
     * todo:文件重命名
     * @return JsonResponse
     */
    public function renameFile(Request $request)
    {
        validatePost($request->get('item'), $this->post, ['oldFile' => 'required|string', 'newFile' => 'required|string']);
        $result = $this->fileService->renameFile($this->post);
        return ajaxReturn($result);
    }
}

<?php

use App\Http\Controllers\Utils\AMap;
use App\Http\Controllers\Utils\Code;
use Curl\Curl;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

if (!function_exists('ajaxReturn')) {
    /**
     * TODO:返回JSON数据
     * @param array $data
     * @param int $code
     * @return JsonResponse
     */
    function ajaxReturn(array $data = [], int $code = 200)
    {
        $_item = array(
            'item' => $data,
            'code' => $code,
            'url' => substr_replace(config('app.url'), '', strlen(config('app.url')) - 1) . request()->getRequestUri()
        );
        saveLog(array('url' => $_item['url'], 'message' => $data['message'] ?? 'successfully', 'response_params' => $data['lists'] ?? ''));
        return response()->json($_item);
    }
}
if (!function_exists('validatePost')) {
    /**
     * TODO:错误信息输出
     * @param $post
     * @param $rules
     * @param array $message
     * @return void
     */
    function validatePost($post, $rules, array $message = [])
    {
        $_validate = Validator::make($post, $rules, $message);
        if ($_validate->fails()) {
            $_code = $_validate->errors()->first() == 'Permission denied' ? Code::FORBIDDEN : 200;
            setCode($_code);
            $_data = array(
                'code' => $_code,
                'item' => array('code' => Code::ERROR, 'message' => $_validate->errors()->first()),
                'url'  => substr_replace(config('app.url'), '', strlen(config('app.url')) - 1) . request()->getRequestUri()
            );
            exit(json_encode($_data, JSON_UNESCAPED_UNICODE));
        }
    }
}
if (!function_exists('saveLog')) {
    /**
     * TODO:日志保存
     * @param $form
     * @return array | bool
     */
    function saveLog($form)
    {
        try {
            $_post = request()->post();
            $_user = $_post['unauthorized'] ?? (object)['username' => 'tourist'];
            if (!empty($_post['role'])) {
                unset($_post['role']);
            }
            $data = array(
                'username'   => $_user->username ?? 'tourist',
                'url'        => $form['url'],
                'ip_address' => request()->getClientIp(),
                'created_at' => time(),
                'day'        => date('Ymd', time()),
                'log'        => json_encode(['message' => $form['message'], 'request_params' => $_post, 'response_params' => $form['response_params']], JSON_UNESCAPED_UNICODE)
            );
            return DB::table('os_system_log')->insert($data);
        } catch (\Exception $exception) {
            return ['code' => Code::SERVER_ERROR, 'message' => $exception->getMessage()];
        }
    }
}
if (!function_exists('getCityCode')) {
    /**
     * TODO:获取城市CODE
     * @return bool|mixed|string
     */
    function getCityCode()
    {
        try {
            $address = (array)(Amap::getInstance()->getAddress(request()->getClientIp()));
            return $address['adcode'] ?? 110000;
        } catch (\Exception $exception) {
            return ['code' => Code::SERVER_ERROR, 'message' => $exception->getMessage()];
        }
    }
}
if (!function_exists('getRoundNum')) {
    /**
     * TODO:生成随机字符串
     * @param $length
     * @param $type
     * @return string
     */
    function getRoundNum($length, $type)
    {
        switch ($type) {
            case 'all':
                $str = '0123456789qwertyuioplkjhgfdsazxcvbnmQWERTYUIOPLKJHGFDSAZXCVBNM';
                break;
            case 'number':
                $str = '123456789';
                break;
            case 'str':
                $str = 'qwertyuioplkjhgfdsazxcvbnmQWERTYUIOPLKJHGFDSAZXCVBNM';
                break;
            case 'large':
                $str = 'WERTYUIOPLKJHGFDSAZXCVBNM';
                break;
            case 'small':
                $str = 'qwertyuioplkjhgfdsazxcvbnm';
                break;
            default:
                $str = time() . uniqid();
                break;
        }
        $char = '';
        for ($i = 0; $i < $length; $i++) {
            $char .= mb_substr(str_shuffle($str), 0, 1);
        }
        return $char;
    }
}
if (!function_exists('getServerIp')) {
    /**
     * TODO:获取服务器ip地址
     * @return array|false|string
     */
    function getServerIp()
    {
        $preg = "/\A((([0-9]?[0-9])|(1[0-9]{2})|(2[0-4][0-9])|(25[0-5]))\.){3}(([0-9]?[0-9])|(1[0-9]{2})|(2[0-4][0-9])|(25[0-5]))\Z/";
        //获取windows Mac
        if (in_array(PHP_OS, ['WINNT', 'Darwin'])) {
            exec("ipconfig", $out, $stats);
            if (!empty($out)) {
                foreach ($out as $row) {
                    if (strstr($row, "IP") && strstr($row, ":") && !strstr($row, "IPv6")) {
                        $tmpIp = explode(":", $row);
                        if (preg_match($preg, trim($tmpIp[1]))) {
                            return trim($tmpIp[1]);
                        }
                    }
                }
            }
        } else {
            //获取操作系统为linux类型的本机IP真实地址
            $result = shell_exec("/sbin/ifconfig");
            if (preg_match_all("/inet (\d+\.\d+\.\d+\.\d+)/", $result, $match) !== 0) {
                foreach ($match [0] as $k => $v) {
                    if ($match [1] [$k] != "127.0.0.1") {
                        return $match [1] [$k];
                    }
                }
            }
        }
        return '127.0.0.1';
    }
}
if (!function_exists('setCode')) {
    /**
     * TODO: 发送HTTP状态
     * @param $code
     */
    function setCode($code)
    {
        static $_status = array(
            // Informational 1xx
            100 => 'Continue',
            101 => 'Switching Protocols',
            // Success 2xx
            200 => 'OK',
            201 => 'Created',
            202 => 'Accepted',
            203 => 'Non-Authoritative Information',
            204 => 'No Content',
            205 => 'Reset Content',
            206 => 'Partial Content',
            // Redirection 3xx
            300 => 'Multiple Choices',
            301 => 'Moved Permanently',
            302 => 'Found', // 1.1
            303 => 'See Other',
            304 => 'Not Modified',
            305 => 'Use Proxy',
            // 306 is deprecated but reserved
            307 => 'Temporary Redirect',
            // Client Error 4xx
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            406 => 'Not Acceptable',
            407 => 'Proxy Authentication Required',
            408 => 'Request Timeout',
            409 => 'Conflict',
            410 => 'Gone',
            411 => 'Length Required',
            412 => 'Precondition Failed',
            413 => 'Request Entity Too Large',
            414 => 'Request-URI Too Long',
            415 => 'Unsupported Media Type',
            416 => 'Requested Range Not Satisfiable',
            417 => 'Expectation Failed',
            // Server Error 5xx
            500 => 'Internal Server Error',
            501 => 'Not Implemented',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
            505 => 'HTTP Version Not Supported',
            509 => 'Bandwidth Limit Exceeded'
        );
        if (isset($_status[$code])) {
            header('HTTP/1.1 ' . $code . ' ' . $_status[$code]);
        }
    }
}
if (!function_exists('getFileLists')) {
    /**
     * TODO:获取文件列表
     * @param $filePath
     * @param array $permissionFile
     * @param int $sort_order
     * @param bool $recursion
     * @return array
     */
    function getFileLists($filePath, array $permissionFile = [], int $sort_order = SORT_ASC, bool $recursion = false)
    {
        try {
            $fileArr = array();
            $_defaultPermission = [
                '.',
                '..',
                'vendor',
                '.gitattributes',
                '.git',
                '.gitignore',
                '.env',
                '.idea',
                '.editorconfig',
                '.DS_Store',
                'node_modules',
                '.styleci.yml',
                'db.php',
                'rsa',
                'css',
                'js',
                'static',
                'favicon.ico',
                'backup'
            ];
            foreach ($permissionFile as $permission) {
                $_defaultPermission[] = $permission;
            }
            $openDir = opendir($filePath);
            $fileType = [];
            while ($file = readdir($openDir)) {
                if (!in_array($file, $_defaultPermission) && str_replace('/', '\\', $filePath . $file) != public_path('storage')) {
                    $fileArr[] = array(
                        'filename'  => $file,
                        'file_type' => filetype($filePath . $file),
                        'children'  => [],
                        'path'      => filetype($filePath . $file) == 'dir' ? $filePath . $file . '/' : $filePath . $file,
                        'md5'       => md5($filePath . $file),
                        'auth'      => chmodFile($filePath . $file),
                        'time'      => date('Y-m-d H:i:s', fileatime($filePath . $file)),
                        'size'      => formatBates(filesize($filePath . $file))
                    );
                    $fileType[] = filetype($filePath . $file);
                }
            }
            /* TODO:是否递归操作 */
            if ($recursion) {
                foreach ($fileArr as &$item) {
                    if ($item['file_type'] === 'dir') {
                        $item['children'] = getFileLists($item['path'], $permissionFile);
                    }
                }
            }
            array_multisort($fileType, $sort_order, $fileArr);
            return $fileArr;
        } catch (\Exception $exception) {
            return ['code' => Code::SERVER_ERROR, 'message' => $exception->getMessage()];
        }
    }
}
if (!function_exists('chmodFile')) {
    /**
     * TODO:获取权限
     * @param $filepath
     * @return bool|string
     */
    function chmodFile($filepath)
    {
        return substr(base_convert(@fileperms($filepath), 10, 8), -3);
    }
}
if (!function_exists('getFilePath')) {
    /**
     * TODO:文件路径
     * @param $path
     * @param $basename
     * @return false|string
     */
    function getFilePath($path, $basename)
    {
        $filePath = array('base_path' => base_path($basename), 'storage_path' => storage_path($basename), 'public_path' => public_path($basename));
        return $basename === '/' ? substr($filePath[$path], 0, strlen($filePath[$path]) - 1) : $basename;
    }
}
if (!function_exists('getFileContent')) {
    /**
     * TODO: 文件读取
     * @param $filepath
     * @return array | string
     */
    function getFileContent($filepath)
    {
        try {
            $fileObj = new SplFileObject($filepath, 'r');
            $fileInfo = '';
            while ($fileObj->valid()) {
                $fileInfo .= $fileObj->fgets();
            }
            $fileObj = null;
            return $fileInfo;
        } catch (\Exception $exception) {
            return ['code' => Code::SERVER_ERROR, 'message' => $exception->getMessage()];
        }
    }
}
if (!function_exists('writeFile')) {
    /**
     * TODO:文件写入
     * @param $filepath
     * @param $content
     * @return int | array
     */
    function writeFile($filepath, $content)
    {
        try {
            $file = new SplFileObject($filepath, 'w');
            return $file->fwrite($content);
        } catch (\Exception $exception) {
            return ['code' => Code::SERVER_ERROR, 'message' => $exception->getMessage()];
        }
    }
}
if (!function_exists('renameFile')) {
    /**
     * TODO:文件重命名
     * @param $oldFile
     * @param $newFile
     * @return bool | array
     */
    function renameFile($oldFile, $newFile)
    {
        try {
            if ((is_file($oldFile) || is_dir($oldFile)) && !file_exists($newFile)) {
                return rename($oldFile, $newFile);
            }
            return ['code' => Code::ERROR, 'message' => 'rename file failed'];
        } catch (\Exception $exception) {
            return ['code' => Code::SERVER_ERROR, 'message' => $exception->getMessage()];
        }
    }
}
if (!function_exists('createFile')) {
    /**
     * TODO:保存文件
     * @param $filepath
     * @return bool | array
     */
    function createFile($filepath)
    {
        try {
            if (file_exists($filepath)) {
                return false;
            }
            $file = mb_substr($filepath, strripos($filepath, '/') + 1);
            if (strstr($file, '.') === false) {
                mkdir($filepath, 0666);
                return true;
            }
            $fileObj = new SplFileObject($filepath, 'a');
            return $fileObj->fwrite(basename($filepath));
        } catch (\Exception $exception) {
            return ['code' => Code::SERVER_ERROR, 'message' => $exception->getMessage()];
        }
    }
}
if (!function_exists('gZipFile')) {
    /**
     * TODO:文件压缩
     * @param array $docLists 文件列表
     * @param string $zipProductPath 文件路径
     * @param string $filename 文件名
     * @return bool | array
     */
    function gZipFile(array $docLists, string $zipProductPath, string $filename)
    {
        try {
            $zipObj = new ZipArchive();
            $result = $zipObj->open($zipProductPath . $filename, ZipArchive::CREATE);
            if (!$result) {
                return false;
            }
            foreach ($docLists as $item) {
                is_dir($item) ? addFileToZip($item, $zipObj) : $zipObj->addFile($item);
            }
            $zipObj->close();
            return file_exists($zipProductPath . $filename);
        } catch (\Exception $exception) {
            return ['code' => Code::SERVER_ERROR, 'message' => $exception->getMessage()];
        }
    }
}
if (!function_exists('unGZipFile')) {
    /**
     * TODO:文件解压
     * @param string $path 文件路径
     * @param string $resource 解压包文件名称
     * @param boolean $removeResource
     * @return bool | array
     */
    function unGZipFile(string $path, string $resource = '', bool $removeResource = false)
    {
        try {
            if (!file_exists($path)) {
                return false;
            }
            $zip = new ZipArchive();
            $arr = explode('.', basename($path));
            if (in_array($arr[1], ['zip', 'ZIP'])) {
                $filePath = str_replace(basename($path), '', $path) . $resource;
                if (!is_dir($filePath)) {
                    mkdir($filePath, 0777, true);
                }
                /* 打开zip文件返回bool值，将完整的归档或给定文件提取到指定的目标 */
                if ($zip->open($path)) {
                    $zip->extractTo($filePath);
                }
                /* 关闭资源 */
                $zip->close();
            }
            return !$removeResource || removeFiles($path);
        } catch (\Exception $exception) {
            return ['code' => Code::SERVER_ERROR, 'message' => $exception->getMessage()];
        }
    }
}
if (!function_exists('addFileToZip')) {
    /**
     * TODO:zip文件压缩
     * @param $path
     * @param ZipArchive $zip
     * @return array | bool
     */
    function addFileToZip($path, ZipArchive $zip)
    {
        try {
            $bool = opendir($path);
            while ($filename = readdir($bool)) {
                if (!in_array($filename, ['.', '..'])) {
                    is_dir($path . DIRECTORY_SEPARATOR . $filename) ? addFileToZip($path . DIRECTORY_SEPARATOR . $filename, $zip) : $zip->addFile($path . DIRECTORY_SEPARATOR . $filename);
                }
            }
            @closedir($path);
            return true;
        } catch (\Exception $exception) {
            return ['code' => Code::SERVER_ERROR, 'message' => $exception->getMessage()];
        }
    }
}
if (!function_exists('removeFiles')) {
    /**
     * TODO:删除文件
     * @param $path
     * @return bool | array
     */
    function removeFiles($path)
    {
        try {
            if (is_file($path)) {
                return unlink($path);
            }
            //先删除目录下的文件：
            $dh = opendir($path);
            while ($file = readdir($dh)) {
                if (!in_array($file, ['.', '..'])) {
                    $fullPath = $path . DIRECTORY_SEPARATOR . $file;
                    !is_dir($fullPath) ? unlink($fullPath) : removeFiles($fullPath);
                }
            }
            @closedir($dh);
            return rmdir($path);
        } catch (\Exception $exception) {
            return ['code' => Code::SERVER_ERROR, 'message' => $exception->getMessage()];
        }
    }
}
if (!function_exists('emptyDir')) {
    /**
     * TODO:判断目录是否为空
     * @param $path
     * @return bool | array
     */
    function emptyDir($path)
    {
        try {
            return !empty(array_diff(scandir($path), array('..', '.')));
        } catch (\Exception $exception) {
            return ['code' => Code::SERVER_ERROR, 'message' => $exception->getMessage()];
        }
    }
}
if (!function_exists('cuttingFile')) {

    /**
     * TODO:文件的分割
     * @param string $filename 文件名
     * @param string $block 文件分割大小
     * @return bool | array
     */
    function cuttingFile(string $filename, string $block = '1024')
    {
        try {
            if (!file_exists($filename)) {
                return false;
            }
            $inputFileObj = new SplFileObject($filename);
            if (!in_array(substr(sprintf('%o', $inputFileObj->getPerms()), -4), [0777, 777])) {
                chmod($filename, 0777);
            }
            while ($content = $inputFileObj->fread($block)) {
                $cutFileName = $inputFileObj->getPath() . DIRECTORY_SEPARATOR . explode('.', $inputFileObj->getFilename())[0] . '_' . uniqid() . '.' . $inputFileObj->getExtension();
                $cutFileObj = new SplFileObject($cutFileName, 'w+');
                $cutFileObj->fwrite($content);
                $cutFileObj = null;
            }
            $inputFileObj = null;
            removeFiles($filename);
            return true;
        } catch (\Exception $exception) {
            return ['code' => Code::SERVER_ERROR, 'message' => $exception->getMessage()];
        }
    }
}
if (!function_exists('mergerFile')) {
    /**
     * TODO:合并文件
     * @param $targetFile
     * @param $filePath
     * @return array | true
     */
    function mergerFile($targetFile, $filePath)
    {
        try {
            $targetFileObj = new SplFileObject($targetFile, 'w+');
            $content = '';
            foreach ($filePath as $file) {
                $fileObj = new SplFileObject($file, 'r');
                while ($fileObj->valid()) {
                    $content .= $fileObj->fgets();
                }
                $fileObj = null;
            }
            $targetFileObj->fwrite($content);
            $targetFileObj = null;
            return true;
        } catch (\Exception $exception) {
            return ['code' => Code::SERVER_ERROR, 'message' => $exception->getMessage()];
        }
    }
}
if (!function_exists('webPush')) {
    /**
     * TODO:站内消息推送
     * @param $content
     * @param string $type
     * @param string $uid
     * @return bool | array
     */
    function webPush($content, string $uid = '', string $type = 'publish')
    {
        try {
            $push_api_url = config('app.push_url');
            $post_data = array('type' => $type, 'content' => htmlspecialchars($content), 'to' => $uid ?? '');
            $curl = new Curl();
            $curl->setOpt(CURLOPT_SSL_VERIFYPEER, false);
            $curl->setOpt(CURLOPT_SSL_VERIFYHOST, false);
            $curl->post($push_api_url, $post_data);
            if ($curl->error) {
                Log::error($curl->errorCode . ':' . $curl->errorMessage);
                return ['code' => $curl->errorCode, 'message' => $curl->errorMessage];
            }
            return true;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return ['code' => Code::SERVER_ERROR, 'message' => $exception->getMessage()];
        }
    }
}
if (!function_exists('getTree')) {
    /**
     * TODO: 树形结构
     * @param array $data
     * @param $pid
     * @param string $attr
     * @param string $pidAttr
     * @return array
     */
    function getTree(array $data, $pid, string $attr = 'data', string $pidAttr = 'pid')
    {
        try {
            $tree = array();
            foreach ($data as $item) {
                $res = (array)$item;
                if ($res[$pidAttr] == $pid) {
                    $res[$attr] = getTree($data, $res['id'], $attr, $pidAttr);
                    $tree[] = $res;
                }
            }
            return $tree;
        } catch (\Exception $exception) {
            return ['code' => Code::SERVER_ERROR, 'message' => $exception->getMessage()];
        }
    }
}
if (!function_exists('formatBates')) {
    /**
     * TODO:大小格式转换
     * @param String $size
     * @param string $delimiter 分割符
     * @return string | array
     */
    function formatBates(string $size, string $delimiter = '')
    {
        try {
            $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
            for ($i = 0; $size >= 1024 && $i < 5; $i++) {
                $size /= 1024;
            }
            return round($size, 2) . $delimiter . $units[$i];
        } catch (\Exception $exception) {
            return ['code' => Code::SERVER_ERROR, 'message' => $exception->getMessage()];
        }
    }

}

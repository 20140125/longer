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
     * 返回JSON数据
     * @param array $data
     * @param int $timestamp
     * @param int $code
     * @return JsonResponse
     */
    function ajaxReturn(array $data = [], int $timestamp = 0, int $code = 200): JsonResponse
    {
        $_item = array(
            'item' => $data,
            'code' => $code,
            'url' => substr_replace(config('app.url'), '', strlen(config('app.url')) - 1) . request()->getRequestUri(),
            'timestamp' => $timestamp,
        );
        saveLog(array('url' => $_item['url'], 'message' => $data['message'] ?? 'successfully', 'response_params' => $data['lists'] ?? ''));
        return response()->json($_item);
    }
}
if (!function_exists('validatePost')) {
    /**
     * 错误信息输出
     * @param $unauthorized
     * @param array $post
     * @param array $rules
     * @param array $message
     * @return void
     */
    function validatePost($unauthorized, array $post = [], array $rules = [], array $message = [])
    {
        if (!empty($unauthorized['code']) && $unauthorized['code'] !== Code::SUCCESS) {
            $_data = array(
                'code' => 200,
                'item' => $unauthorized,
                'url'  => substr_replace(config('app.url'), '', strlen(config('app.url')) - 1) . request()->getRequestUri()
            );
            exit(json_encode($_data, JSON_UNESCAPED_UNICODE));
        }
        if (!empty($rules)) {
            /* 字段验证 */
            $_validate = Validator::make($post, $rules, $message);
            if ($_validate->fails()) {
                $_data = array(
                    'code' => 200,
                    'item' => array('code' => Code::ERROR, 'message' => $_validate->errors()->first()),
                    'url'  => substr_replace(config('app.url'), '', strlen(config('app.url')) - 1) . request()->getRequestUri()
                );
                exit(json_encode($_data, JSON_UNESCAPED_UNICODE));
            }
        }
    }
}
if (!function_exists('saveLog')) {
    /**
     *  日志保存
     * @param $form
     * @return array|int
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
            return  \App\Models\Api\v1\Log::getInstance()->saveOne($data);
        } catch (\Exception $exception) {
            return ['code' => Code::SERVER_ERROR, 'message' => $exception->getMessage()];
        }
    }
}
if (!function_exists('getCityCode')) {
    /**
     * 获取城市CODE
     * @return bool|mixed|string
     */
    function getCityCode()
    {
        try {
            $address = (array)(Amap::getInstance()->getAddress(request()->getClientIp()));
            return gettype($address['adcode']) == 'array' ? 110000 : $address['adcode'];
        } catch (\Exception $exception) {
            return ['code' => Code::SERVER_ERROR, 'message' => $exception->getMessage()];
        }
    }
}
if (!function_exists('getRoundNum')) {
    /**
     * 生成随机字符串
     * @param $length
     * @param $type
     * @return string
     */
    function getRoundNum($length, $type): string
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
     * 获取服务器ip地址
     * @return string
     */
    function getServerIp(): string
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
     *  发送HTTP状态
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
     * 获取文件列表
     * @param $filePath
     * @param array $permissionFile
     * @param int $sort_order
     * @param bool $recursion
     * @return array
     */
    function getFileLists($filePath, array $permissionFile = [], int $sort_order = SORT_ASC, bool $recursion = false): array
    {
        try {
            $fileArr = array();
            $_defaultPermission = ['.', '..'];
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
                        'name'       => md5($filePath . $file),
                        'auth'      => chmodFile($filePath . $file),
                        'time'      => date('Y-m-d H:i:s', fileatime($filePath . $file)),
                        'size'      => formatBates(filesize($filePath . $file))
                    );
                    $fileType[] = filetype($filePath . $file);
                }
            }
            /* 是否递归操作 */
            if ($recursion) {
                foreach ($fileArr as $index => &$item) {
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
     * 获取权限
     * @param $filepath
     * @return array|false|string
     */
    function chmodFile($filepath)
    {
        try {
            return substr(base_convert(@fileperms($filepath), 10, 8), -3);
        } catch (\Exception $exception) {
            return ['code' => Code::SERVER_ERROR, 'message' => $exception->getMessage()];
        }
    }
}
if (!function_exists('getFilePath')) {
    /**
     * 文件路径
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
     *  文件读取
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
     * 文件写入
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
     * 文件重命名
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
     * 保存文件
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
     * 文件压缩
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
     * 文件解压
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
     * zip文件压缩
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
     * 删除文件
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
     * 判断目录是否为空
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
     * 文件的分割
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
     * 合并文件
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
     * 站内消息推送
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
     *  树形结构
     * @param array $data
     * @param $pid
     * @param string $attr
     * @param string $pidAttr
     * @return array
     */
    function getTree(array $data, $pid, string $attr = 'data', string $pidAttr = 'pid'): array
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
     * 大小格式转换
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
if (!function_exists('getFirstChar')) {
    /**
     * 获取中文首字母
     * @param string $s0
     * @return array | string
     */
    function getFirstChar(string $s0)
    {
        try {
            $char = ord(substr($s0, 0, 1));
            if ($char >= ord('A') and $char <= ord('z')) {
                return substr(strtoupper($s0), 0, 1);
            }
            $s1  = iconv('UTF-8', 'GB2312', $s0);
            $s2  = iconv('GB2312', 'UTF-8', $s1);
            $s = $s2 == $s0 ? $s1 : $s0;
            $asc = ord($s) * 256 + ord($s) - 65536;
            if ($asc >= -20319 and $asc <= -20284) {
                return 'A';
            }
            if ($asc >= -20283 and $asc <= -19776) {
                return 'B';
            }
            if ($asc >= -19775 and $asc <= -19219) {
                return 'C';
            }
            if ($asc >= -19218 and $asc <= -18711) {
                return 'D';
            }
            if ($asc >= -18710 and $asc <= -18527) {
                return 'E';
            }
            if ($asc >= -18526 and $asc <= -18240) {
                return 'F';
            }
            if ($asc >= -18239 and $asc <= -17923) {
                return 'G';
            }
            if ($asc >= -17922 and $asc <= -17418) {
                return 'H';
            }
            if ($asc >= -17417 and $asc <= -16475) {
                return 'J';
            }
            if ($asc >= -16474 and $asc <= -16213) {
                return 'K';
            }
            if ($asc >= -16212 and $asc <= -15641) {
                return 'L';
            }
            if ($asc >= -15640 and $asc <= -15166) {
                return 'M';
            }
            if ($asc >= -15165 and $asc <= -14923) {
                return 'N';
            }
            if ($asc >= -14922 and $asc <= -14915) {
                return 'O';
            }
            if ($asc >= -14914 and $asc <= -14631) {
                return 'P';
            }
            if ($asc >= -14630 and $asc <= -14150) {
                return 'Q';
            }
            if ($asc >= -14149 and $asc <= -14091) {
                return 'R';
            }
            if ($asc >= -14090 and $asc <= -13319) {
                return 'S';
            }
            if ($asc >= -13318 and $asc <= -12839) {
                return 'T';
            }
            if ($asc >= -12838 and $asc <= -12557) {
                return 'W';
            }
            if ($asc >= -12556 and $asc <= -11848) {
                return 'X';
            }
            if ($asc >= -11847 and $asc <= -11056) {
                return 'Y';
            }
            if ($asc >= -11055 and $asc <= -10247) {
                return 'Z';
            }
            return null;
        } catch (\Exception $exception) {
            return ['code' => Code::SERVER_ERROR, 'message' => $exception->getMessage()];
        }
    }
}
if (!function_exists('getXingLists')) {
    /**
     * 获取姓氏
     * @return string[]
     */
    function getXingLists(): array
    {
        return array(
            '赵','钱','孙','李','周','吴','郑','王','冯','陈','褚','卫','蒋','沈','韩','杨','朱','秦','尤','许','何','吕','施','张','孔',
            '曹','严','华','金','魏','陶','姜','戚','谢','邹', '喻','柏','水','窦','章','云','苏','潘','葛','奚','范','彭','郎','鲁','韦',
            '昌','马','苗','凤','花','方','任','袁','柳','鲍','史','唐','费','薛','雷','贺','倪','汤','滕','殷','罗', '毕','郝','安','常',
            '傅','卞','齐','元','顾','孟','平','黄','穆','萧','尹','姚','邵','湛','汪','祁','毛','狄','米','伏','成','戴','谈','宋','茅',
            '庞','熊','纪','舒','屈','项','祝', '董','梁','杜','阮','蓝','闵','季','贾','路','娄','江','童','颜','郭','梅','盛','林','钟',
            '徐','邱','骆','高','夏','蔡','田','樊','胡','凌','霍','虞','万','支','柯','管','卢','莫', '柯','房','裘','缪','解','应','宗',
            '丁','宣','邓','单','杭','洪','包','诸','左','石','崔','吉','龚','程','嵇','邢','裴','陆','荣','翁','荀','于','惠','甄','曲',
            '封','储','仲','伊', '宁','仇','甘','武','符','刘','景','詹','龙','叶','幸','司','黎','溥','印','怀','蒲','邰','从','索','赖',
            '卓','屠','池','乔','胥','闻','莘','党','翟','谭','贡','劳','逄','姬','申', '扶','堵','冉','宰','雍','桑','寿','通','燕','浦',
            '尚','农','温','别','庄','晏','柴','瞿','阎','连','习','容','向','古','易','廖','庾','终','步','都','耿','满','弘','匡','国','文',
            '寇','广','禄','阙','东','欧','利','师','巩','聂','关','荆','司马','上官','欧阳','夏侯','诸葛','闻人','东方','赫连','皇甫','尉迟',
            '公羊','澹台','公冶','宗政','濮阳','淳于','单于','太叔','申屠','公孙','仲孙','轩辕','令狐','徐离','宇文','长孙','慕容','司徒','司空'
        );
    }
}
if (!function_exists('getMingLists')) {
    /**
     * 获取名字
     * @return string[]
     */
    function getMingLists(): array
    {
        return array(
            '伟','刚','勇','毅','俊','峰','强','军','平','保','东','文','辉','力','明','永','健','世','广','志','义','兴','良','海','山','仁','波',
            '宁','贵','福','生','龙','元','全','国','胜','学','祥','才','发','武','新','利','清','飞','彬','富','顺','信','子','杰','涛','昌','成',
            '康','星','光','天','达','安','岩','中','茂','进','林','有','坚','和','彪','博','诚','先','敬','震','振','壮','会','思','群','豪','心',
            '邦','承','乐','绍','功','松','善','厚','庆','磊','民','友','裕','河','哲','江','超','浩','亮','政','谦','亨','奇','固','之','轮','翰',
            '朗','伯','宏','言','若','鸣','朋','斌','梁','栋','维','启','克','伦','翔','旭','鹏','泽','晨','辰','士','以','建','家','致','树','炎',
            '德','行','时','泰','盛','雄','琛','钧','冠','策','腾','楠','榕','风','航','弘','秀','娟','英','华','慧','巧','美','娜','静','淑','惠',
            '珠','翠','雅','芝','玉','萍','红','娥','玲','芬','芳','燕','彩','春','菊','兰','凤','洁','梅','琳','素','云','莲','真','环','雪','荣',
            '爱','妹','霞','香','月','莺','媛','艳','瑞','凡','佳','嘉','琼','勤','珍','贞','莉','桂','娣','叶','璧','璐','娅','琦','晶','妍','茜',
            '秋','珊','莎','锦','黛','青','倩','婷','姣','婉','娴','瑾','颖','露','瑶','怡','婵','雁','蓓','纨','仪','荷','丹','蓉','眉','君','琴',
            '蕊','薇','菁','梦','岚','苑','婕','馨','瑗','琰','韵','融','园','艺','咏','卿','聪','澜','纯','毓','悦','昭','冰','爽','琬','茗','羽',
            '希','欣','飘','育','滢','馥','筠','柔','竹','霭','凝','晓','欢','霄','枫','芸','菲','寒','伊','亚','宜','可','姬','舒','影','荔','枝',
            '丽','阳','妮','宝','贝','初','程','梵','罡','恒','鸿','桦','骅','剑','娇','纪','宽','苛','灵','玛','媚','琪','晴','容','睿','烁','堂',
            '唯','威','韦','雯','苇','萱','阅','彦','宇','雨','洋','忠','宗','曼','紫','逸','贤','蝶','菡','绿','蓝','儿','翠','烟'
        );
    }
}

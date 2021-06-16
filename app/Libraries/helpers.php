<?php

use App\Http\Controllers\Utils\Amap;
use App\Http\Controllers\Utils\Code;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

if (!function_exists('ajaxReturn')) {
    /**
     * todo:返回JSON数据
     * @param array $data
     * @param int $code
     * @return JsonResponse
     */
    function ajaxReturn($data = [], $code = 200)
    {
        $_item = array('item' => $data, 'code' => $code, 'url' => substr_replace(config('app.url'), '', strlen(config('app.url')) - 1).request()->getRequestUri());
        saveLog(array('url' => $_item['url'], 'message' => $data['message'] ?? 'successfully'));
        return response()->json($_item);
    }
}
if (!function_exists('validatePost')) {
    /**
     * todo:错误信息输出
     * @param $post
     * @param $rules
     * @param array $message
     * @return void
     */
    function validatePost($post, $rules, $message = [])
    {
        $_validate = Validator::make($post, $rules, $message);
        if ($_validate->fails()) {
            $_code = $_validate->errors()->first() == 'Permission denied' ? Code::FORBIDDEN : Code::ERROR;
            setCode($_code);
            $_data =  array(
                'code' => $_code,
                'msg' => $_validate->errors()->first(),
                'url' => substr_replace(config('app.url'), '', strlen(config('app.url')) - 1).request()->getRequestUri()
            );
            exit(json_encode($_data,JSON_UNESCAPED_UNICODE));
        }
    }
}
if (!function_exists('saveLog')) {
    /**
     * todo:日志保存
     * @param $form
     */
    function saveLog($form)
    {
        $_post = request()->post();
        $_user = $_post['unauthorized'] ?? (object)['username' => 'tourist'];
        if (!empty($_post['role'])) {
            unset($_post['role']);
        }
        $data = array(
            'username' => $_user->username ?? 'tourist',
            'url' =>  $form['url'],
            'ip_address' =>getServerIp(),
            'created_at' =>time(),
            'day' => date('Ymd'),
            'log' => json_encode(['message' => $form['message'], 'request_params' => $_post], JSON_UNESCAPED_UNICODE)
        );
        DB::table('os_system_log')->insert($data);
    }
}
if (!function_exists('getCityCode')) {
    /**
     * todo:获取城市CODE
     * @return bool|mixed|string
     */
    function getCityCode()
    {
        try {
            $address = (array)(Amap::getInstance()->getAddress(getServerIp()));
            return $address['adcode'] ?? '110000';
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return false;
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
                $str = time().uniqid();
                break;
        }
        $char='';
        for ($i=0; $i<$length; $i++) {
            $char.= mb_substr(str_shuffle($str), 0, 1);
        }
        return $char;
    }
}
if (!function_exists('getServerIp')) {
    /**
     * todo：获取服务器ip地址
     * @return array|false|string
     */
    function getServerIp()
    {
        $preg = "/\A((([0-9]?[0-9])|(1[0-9]{2})|(2[0-4][0-9])|(25[0-5]))\.){3}(([0-9]?[0-9])|(1[0-9]{2})|(2[0-4][0-9])|(25[0-5]))\Z/";
        //获取windows Mac
        if (in_array(PHP_OS, ['WINNT','Darwin'])) {
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
     * todo: 发送HTTP状态
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
            header('HTTP/1.1 '.$code.' '.$_status[$code]);
        }
    }
}

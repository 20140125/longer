<?php

namespace App\Http\Controllers\Utils;

use App\Http\Controllers\Controller;

class Code extends Controller
{
    /* 成功 */
    const SUCCESS = 20000;
    /* 通用失败码 */
    const ERROR = 20001;
    /* 邮箱验证失败码 */
    const VERIFY_CODE_ERROR = 20002;
    /* 没有登录 */
    const UNAUTHORIZED = 401;
    /* 拒绝访问 */
    const FORBIDDEN = 403;
    /* 页面不存在 */
    const NOT_FOUND = 404;
    /* 方法不允许 */
    const METHOD_ERROR = 405;
    /* 服务器错误 */
    const SERVER_ERROR = 500;
    /* 站内信息推送的状态 (成功 失败 离线) */
    const WEBSOCKET_STATE = array('successfully', 'failed', 'offline');
    /**
     * @var static $instance
     */
    protected static $instance;

    /**
     * @return Code|static
     */
    public static function getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    private function __clone()
    {
        // TODO: Implement __clone() method.
    }
}

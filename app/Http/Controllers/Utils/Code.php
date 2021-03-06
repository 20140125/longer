<?php

namespace App\Http\Controllers\Utils;

/**
 * Class Code
 * @author <fl140125@gmail.com>
 * @package App\Http\Controllers\Utils
 */
class Code
{
    //成功
    const SUCCESS = 200;
    //失败
    const ERROR = 201;
    //方法不允许
    const METHOD_ERROR = 405;
    //没有权限
    const NOT_ALLOW = 403;
    //没有登录
    const UNAUTHORIZED = 401;
    //页面不存在
    const NOT_FOUND = 404;
    //邮箱验证失败码
    const VERIFY_CODE = 202;
    //服务器错误
    const SERVER_ERROR = 500;
    // 站内信息推送的状态 (成功 失败 离线)
    const WEBSOCKET_STATE = array( 'successfully', 'failed', 'offline');
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

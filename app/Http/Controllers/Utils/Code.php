<?php

namespace App\Http\Controllers\Utils;

use App\Http\Controllers\Controller;

class Code extends Controller
{
    /* 成功 */
    const SUCCESS = '20000';
    /* 通用失败码 */
    const ERROR = '20001';
    /* 邮箱验证失败码 */
    const VERIFY_CODE_ERROR = '20002';
    /* 没有登录系统 */
    const NOT_LOGIN = '20003';
    /* 没有权限 */
    const UNAUTHORIZED = '40001';
    /* 非法TOKEN操作 */
    const VALID_TOKEN = '40002';
    /* 拒绝访问 */
    const FORBIDDEN = '40003';
    /* 页面不存在 */
    const NOT_FOUND = '40004';
    /* 方法不允许 */
    const METHOD_ERROR = '40005';
    /* 服务器错误 */
    const SERVER_ERROR = '50000';
    /* 站内信息推送的状态 (成功 失败 离线) */
    const WEBSOCKET_STATE = array('successfully', 'failed', 'offline');
    /* 禁止访问地址 */
    const FORBIDDEN_MESSAGE = 'Forbidden Access URL';
    /* 单次请求记录超过限制 */
    const PAGE_SIZE_MESSAGE = 'Exceeded Single Page Request Record Limit';
    /* 非法操作 */
    const VALID_TOKEN_MESSAGE = 'Invalid Token';
    /* 没有登录 */
    const NOT_LOGIN_MESSAGE = 'Please Login System';
    /* 令牌为空 */
    const TOKEN_EMPTY_MESSAGE = 'Token Is Not Provided';
    /* 令牌过期 */
    const TOKEN_EXPIRED_MESSAGE = 'Token Is Expired';
    /* 用户被禁用 */
    const USER_DISABLED_MESSAGE = 'User Is Disabled';
    /* 角色不存在 */
    const ROLE_NOT_EXIST_MESSAGE = 'Role Is Not Exists';
    /* 角色被禁用 */
    const ROLE_DISABLED_MESSAGE = 'Role Is Disabled';
    /* 用户不存在 */
    const USER_NOT_FOUND_MESSAGE = 'User Not Found';
    /* 方法不被允许 */
    const METHOD_ERROR_MESSAGE = 'Method Not Allowed';


    /**
     * @var static $instance
     */
    protected static $instance;

    /**
     * @return Code|static
     */
    public static function getInstance(): Code
    {
        if (!self::$instance instanceof self) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    private function __clone()
    {
        //  Implement __clone() method.
    }
}

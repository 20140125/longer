<?php

namespace App\Http\Controllers\Utils;

use App\Http\Controllers\Controller;

class Code extends Controller
{
    /* todo:成功 */
    const SUCCESS = '20000';
    /* todo:通用失败码 */
    const ERROR = '20001';
    /* todo:邮箱验证失败码 */
    const VERIFY_CODE_ERROR = '20002';
    /* todo:没有登录 */
    const UNAUTHORIZED = '40001';
    /* todo:拒绝访问 */
    const FORBIDDEN = '40003';
    /* todo:页面不存在 */
    const NOT_FOUND = '40004';
    /* todo:方法不允许 */
    const METHOD_ERROR = '40005';
    /* todo:服务器错误 */
    const SERVER_ERROR = '50000';
    /* todo:站内信息推送的状态 (成功 失败 离线) */
    const WEBSOCKET_STATE = array('successfully', 'failed', 'offline');
    /* todo:禁止访问地址 */
    const FORBIDDEN_MESSAGE = 'Forbidden Access URL';
    /* todo:单次请求记录超过限制 */
    const PAGE_SIZE_MESSAGE = 'Exceeded Single Page Request Record Limit';
    /* todo:没有登录 */
    const NOT_LOGIN_MESSAGE = 'Please Login System';
    /* todo:令牌为空 */
    const TOKEN_EMPTY_MESSAGE = 'Token Is Not Provided';
    /* todo:令牌过期 */
    const TOKEN_EXPIRED_MESSAGE = 'Token Is Expired';
    /* todo:用户被禁用 */
    const USER_DISABLED_MESSAGE = 'User Is Disabled';
    /* todo:角色不存在 */
    const ROLE_NOT_EXIST_MESSAGE = 'Role Is Not Exists';
    /* todo:角色被禁用 */
    const ROLE_DISABLED_MESSAGE = 'Role Is Disabled';
    /* todo:用户不存在 */
    const USER_NOT_FOUND_MESSAGE = 'User Not Found';


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

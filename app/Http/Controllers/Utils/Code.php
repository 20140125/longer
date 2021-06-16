<?php

namespace App\Http\Controllers\Utils;

class Code
{
    /* todo:成功 */
    const SUCCESS = 20000;
    /* todo:通用失败码 */
    const ERROR = 20001;
    /* todo:邮箱验证失败码 */
    const VERIFY_CODE_ERROR = 20002;
    /* todo:没有登录 */
    const UNAUTHORIZED = 40001;
    /* todo:拒绝访问 */
    const FORBIDDEN = 40003;
    /* todo:页面不存在 */
    const NOT_FOUND = 40004;
    /* todo:方法不允许 */
    const METHOD_ERROR = 40005;
    /* todo:服务器错误 */
    const SERVER_ERROR = 50000;
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

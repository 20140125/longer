<?php

namespace App\Http\Controllers\Utils;


class Code
{
    const SUCCESS = 200;
    const ERROR = 201;
    const METHOD_ERROR = 405;
    const NOT_ALLOW = 403;
    protected static $instance;

    static public function getInstance()
    {
        if (!self::$instance instanceof self){
            self::$instance = new static();
        }
        return self::$instance;
    }
}

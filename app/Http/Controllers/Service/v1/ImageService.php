<?php

namespace App\Http\Controllers\Service\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ImageService extends BaseService
{
    /**
     * @var static $instance
     */
    private static $instance;
    /**
     * @return static
     */
    public static function getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new static();
        }
        return self::$instance;
    }
}

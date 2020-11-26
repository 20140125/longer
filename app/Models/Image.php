<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Class Image
 * @author <fl20140125@gmail.com>
 * @package App\Models
 */
class Image extends Model
{
    /**
     * @var string $table
     */
    public $table = 'os_soogif';
    /**
     * @var static $instance
     */
    protected static $instance;

    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

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

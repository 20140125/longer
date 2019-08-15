<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class Area
 * @author <fl140125@gmail.com>
 * @package App\Models
 */
class Area extends Model
{
    public $table = 'os_china_area';
    /**
     * @var static $instance
     */
    protected static $instance;

    /**
     * @return Area
     */
    static public function getInstance()
    {
        if (!self::$instance instanceof self){
            return self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * TODO:获取所有记录
     * @param array $columns
     * @return Collection
     */
    public function getResultLists($columns=['*'])
    {
        $result = DB::table($this->table)->get($columns);
        return $result;
    }
}

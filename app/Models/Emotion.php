<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Emotion extends Model
{
    /**
     * @var string $table
     */
    public $table = 'os_emotion';
    /**
     * @var static $instance
     */
    protected static $instance;

    /**
     * @return Emotion
     */
    public static function getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * TODO:获取表情
     * @param int $type
     * @param int $page
     * @param int $limit
     * @return mixed
     */
    public function getListByType(int $type, int $page, int $limit)
    {
        $result['data'] = DB::table($this->table)->where('type', $type)->offset($limit*($page-1))->limit($limit)->get();
        $result['pages'] = ceil(DB::table($this->table)->where('type', $type)->count()/$limit);
        return $result;
    }
}

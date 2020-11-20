<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Class TimeLine
 * @author <fl140125@gmail.com>
 * @package App\Models
 */
class TimeLine extends Model
{
    /**
     * @var string $table
     */
    public $table = 'os_timeline';
    /**
     * @var static $instance
     */
    protected static $instance;

    /**
     * @return TimeLine
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

    /**
     * TODO:数据列表
     * @param int $page
     * @param int $limit
     * @return mixed
     */
    public function getResultLists(int $page, int $limit)
    {
        $result['data'] = DB::table($this->table)->offset($limit*($page-1))->limit($limit)->orderByDesc('id')->get();
        $result['total'] = DB::table($this->table)->count();
        return $result;
    }
    /**
     * TODO:保存数据
     * @param array $data
     * @return bool
     */
    public function saveResult(array $data)
    {
        $data['timestamp'] = date('Y-m-d');
        return DB::table($this->table)->insert($data);
    }

    /**
     * TODO:更新数据
     * @param array $data
     * @param string $field
     * @param int $value
     * @return int
     */
    public function updateResult(array $data, string $field, int $value)
    {
        return DB::table($this->table)->where($field, $value)->update($data);
    }
}

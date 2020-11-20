<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class Chat
 * @author <fl140125@gmail.com>
 * @package App\Models
 */
class Chat extends Model
{
    /**
     * @var string $table
     */
    protected $table = 'os_chat';
    /**
     * @var static $instance
     */
    protected static $instance;

    /**
     * @return Chat
     */
    public static function getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * TODO:获取聊天记录
     * @param array $where
     * @param int $limit
     * @param int $page
     * @param array $columns
     * @return mixed
     */
    public function getResult(array $where, int $limit, int $page, array $columns = ['*'])
    {
        $result['data'] = DB::table($this->table)->limit($limit)
            ->offset($limit*($page-1))->where($where)->orderByDesc('id')->get($columns);
        $result['total'] = DB::table($this->table)->where($where)->count();
        return $result;
    }
    /**
     * TODO:保存聊天记录
     * @param array $data
     * @return bool
     */
    public function saveResult(array $data)
    {
        return DB::table($this->table)->insert($data);
    }
}

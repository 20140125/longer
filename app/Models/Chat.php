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
    static public function getInstance()
    {
        if (!self::$instance instanceof self){
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * TODO:获取聊天记录
     * @param $where
     * @param array $columns
     * @return Collection
     */
    public function getResult($where,$columns=['*'])
    {
        return DB::table($this->table)->where($where)->get($columns);
    }

    /**
     * TODO:获取聊天历史记录
     * @param $from_client_name
     * @param $to_client_name
     * @param $page
     * @param $limit
     * @param array $columns
     * @return Collection
     */
    public function getResultLists($from_client_name,$to_client_name,$page,$limit,$columns=['*'])
    {
        $where[] = ['from_client_name',$from_client_name];
        $where[] = ['to_client_name',$to_client_name];
        $whereOr[] = ['to_client_name',$from_client_name];
        $whereOr[] = ['from_client_name',$to_client_name];
        return DB::table($this->table)->where($where)->orWhere($whereOr)->limit($limit)->offset($limit*($page-1))->get($columns);
    }

    /**
     * TODO：聊天记录页数
     * @param $from_client_name
     * @param $to_client_name
     * @param $limit
     * @return float
     */
    public function getTotalPages($from_client_name,$to_client_name,$limit)
    {
        $where[] = ['from_client_name',$from_client_name];
        $where[] = ['to_client_name',$to_client_name];
        $whereOr[] = ['to_client_name',$from_client_name];
        $whereOr[] = ['from_client_name',$to_client_name];
        return  ceil(DB::table($this->table)->where($where)->orWhere($whereOr)->count()/$limit);
    }

    /**
     * TODO:保存聊天记录
     * @param $data
     * @return bool
     */
    public function saveResult($data)
    {
        return DB::table($this->table)->insert($data);
    }
}

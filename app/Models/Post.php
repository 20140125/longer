<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class Post
 * @package App
 * @property string $url
 * @property string $author
 * @property string $content
 * @property string $title
 * @property string $post_date
 * @property string $created_at
 * @property string $updated_at
 */
class Post extends Model
{
    protected static $tableName = 'os_posts';
    private static $instance;

    private function __clone()
    {
        // TODO: Implement __clone() method.
    }
    static public function getInstance()
    {
        if (!self::$instance instanceof self){
            self::$instance = new static();
        }
        return self::$instance;
    }

    protected $fillable = [
        'url',
        'author',
        'title',
        'content',
        'post_date'
    ];
    /**
     * 添加数据
     * @param $data
     * @return bool
     */
    public function addResult($data)
    {
        $result = DB::table(self::$tableName)->insert($data);
        return $result;
    }

    /**
     * 总记录
     * @return int
     */
    public function getCount()
    {
        $result = DB::table(self::$tableName)->count();
        return $result;
    }
    /**
     * 查询一条记录
     * @param $field
     * @param $value
     * @param string $op
     * @param array $column
     * @return Model|Builder|null|object
     */
    public function getResult($field, $value,$op='=', $column = ['*'])
    {
        $result = DB::table(self::$tableName)->where($field,$op,$value)->first($column);
        return $result;
    }

    /**
     * @param $where
     * @param array $column
     * @return Collection
     */
    public function getResultLists($where,$column=['*'])
    {
        $result = DB::table(self::$tableName)->where($where)->limit(100)->get($column);
        return $result;
    }

    /**
     * 更新数据
     * @param $field
     * @param $value
     * @param $data
     * @return int
     */
    public function updateResult($field,$value,$data)
    {
        $result = DB::table(self::$tableName)->whereIn($field,$value)->update($data);
        return $result;
    }
}

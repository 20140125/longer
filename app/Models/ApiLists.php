<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class ApiLists
 * @author <fl140125@gmail.com>
 * @package App\Models
 */
class ApiLists extends Model
{
    /**
     * @var string $table
     */
    public $table = 'os_api_lists';
    /**
     * @var static $instance
     */
    protected static $instance;

    /**
     * @return ApiLists
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
     * TODO: 查询一条记录
     * @param string $field
     * @param int $value
     * @param string $op
     * @param array $column
     * @return Model|Builder|object|null
     */
    public function getResult(string $field, int $value, string $op = '=', array $column = ['*'])
    {
        return DB::table($this->table)->where($field, $op, $value)->first($column);
    }

    /**
     * TODO: 添加记录
     * @param array $data
     * @return int
     */
    public function addResult(array $data)
    {
        $data['request'] = empty($data['request'])? "[]" : json_encode($data['request'], JSON_UNESCAPED_UNICODE);
        $data['response'] = empty($data['response'])? "[]" : json_encode($data['response'], JSON_UNESCAPED_UNICODE);
        $data['response_string'] = json_encode($data['response_string'], JSON_UNESCAPED_UNICODE);
        return DB::table($this->table)->insertGetId($data);
    }

    /**
     * TODO: 更新一条数据
     * @param array $data
     * @param string $field
     * @param int $value
     * @param string $op
     * @return int
     */
    public function updateResult(array $data, string $field, int $value, string $op = '=')
    {
        $data['request'] = empty($data['request'])? "[]" : json_encode($data['request'], JSON_UNESCAPED_UNICODE);
        $data['response'] = empty($data['response'])? "[]" : json_encode($data['response'], JSON_UNESCAPED_UNICODE);
        $data['response_string'] = json_encode($data['response_string'], JSON_UNESCAPED_UNICODE);
        return DB::table($this->table)->where($field, $op, $value)->update($data);
    }

    /**
     * TODO: 删除一条数据
     * @param string $field
     * @param int $value
     * @return int
     */
    public function deleteResult(string $field, int $value)
    {
        return DB::table($this->table)->where($field, $value)->delete();
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Class Config
 * @author <fl140125@gmail.com>
 * @package App\Models
 */
class Config extends Model
{
    /**
     * @var string $table
     */
    public $table = 'os_config';
    /**
     * @var static $instance
     */
    private static $instance;

    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    /**
     * @return Config
     */
    public static function getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * TODO: 查询一条记录
     * @param string $field
     * @param string|int $value
     * @param string $op
     * @param array $column
     * @return Model|Builder|object|null
     */
    public function getResult(string $field, $value, string $op = '=', array $column = ['*'])
    {
        return DB::table($this->table)->where($field, $op, $value)->first($column);
    }

    /**
     * TODO：获取配置列表
     * @return mixed
     */
    public function getResultLists()
    {
        return DB::table($this->table)->get();
    }

    /**
     * TODO: 添加记录
     * @param array $data
     * @return bool
     */
    public function addResult(array $data)
    {
        $data['created_at'] = time();
        $data['updated_at'] = time();
        return DB::table($this->table)->insertGetId($data);
    }

    /**
     * TODO: 更新一条数据
     * @param $data
     * @param string $field
     * @param int $value
     * @param string $op
     * @return int
     */
    public function updateResult($data, string $field, int $value, string $op = '=')
    {
        if (!empty($data['act'])) {
            unset($data['act']);
            return  DB::table($this->table)->where($field, $op, $value)->update($data);
        }
        switch ($data['hasChildren']) {
            case "true":
                unset($data['hasChildren']);
                $intFields = ['status','id'];
                foreach ($intFields as $int) {
                    $data[$int] = (int)$data[$int];
                }
                $data['children'] = json_encode($data['children'], JSON_UNESCAPED_UNICODE);
                $data['created_at'] = empty($data['created_at']) ? time() : strtotime($data['created_at']);
                $data['updated_at'] = empty($data['updated_at']) ? time() : strtotime($data['updated_at']);
                $res = DB::table($this->table)->where($field, $op, $value)->update($data);
                break;
            case "false":
                $result = DB::table($this->table)->where($field, $op, $data['pid'])->first();
                unset($data['hasChildren']);
                $result->children = objectToArray(json_decode($result->children, true));
                $index = 0;
                foreach ($result->children as $key => $child) {
                    if ($data['id'] == $child['id']) {
                        unset($result->children[$key]);
                        $index = $key;
                    }
                }
                $intFields = ['status','id','pid'];
                foreach ($intFields as $int) {
                    $data[$int] = (int)$data[$int];
                }
                $result->children[$index] = $data;
                $children = array();
                $sort = array();
                foreach ($result->children as $item) {
                    array_push($children, $item);
                    $sort[] = $item['id'];
                }
                array_multisort($sort, $children, SORT_ASC);
                $result->children = json_encode($children, JSON_UNESCAPED_UNICODE);
                $res =  DB::table($this->table)->where('id', '=', $data['pid'])->update(objectToArray($result));
                break;
            default:
                $res = 0;
        }
        return $res;
    }

    /**
     * TODO: 删除一条数据
     * @param string $field
     * @param int $value
     * @param string $op
     * @return int
     */
    public function deleteResult(string $field, int $value, string $op = '=')
    {
        return DB::table($this->table)->where($field, $op, $value)->delete();
    }
}

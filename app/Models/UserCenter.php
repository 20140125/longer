<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class UserCenter extends Model
{
    public $table = 'os_user_center';
    /**
     * @var static $instance
     */
    protected static $instance;

    /**
     * @return UserCenter
     */
    public static function getInstance()
    {
        if (!self::$instance instanceof self) {
            return self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * TODO:查询一条记录
     * @param string|array $field
     * @param string|int $value
     * @param string $op
     * @param array $column
     * @return Model|Builder|null|object
     */
    public function getResult($field, $value = '', string $op = '=', array $column = ['*'])
    {
        return DB::table($this->table)->where($field, $op, $value)->first($column);
    }

    /**
     * TODO:  添加记录
     * @param array $data
     * @return bool
     */
    public function addResult(array $data)
    {
        $data['tags'] = empty($data['tags']) ? '0' :
            str_replace('\\', '', json_encode($data['tags'], JSON_UNESCAPED_UNICODE));
        $data['ip_address'] = empty($data['ip_address']) ? '0' :
            str_replace('\\', '', json_encode($data['ip_address'], JSON_UNESCAPED_UNICODE));
        $data['local'] = empty($data['local']) ? '0' :
            str_replace('\\', '', json_encode($data['local'], JSON_UNESCAPED_UNICODE));
        return DB::table($this->table)->insertGetId($data);
    }
    /**
     * TODO:更新一条数据
     * @param array $data
     * @param string|array $field
     * @param string|int|null $value
     * @param string $op
     * @return int
     */
    public function updateResult(array $data, $field, $value = null, $op = '=')
    {
        if (empty($data['type'])) {
            $data['tags'] = (empty($data['tags']) || !isset($data['tags'])) ? '0' :
                str_replace('\\', '', json_encode($data['tags'], JSON_UNESCAPED_UNICODE));
            $data['ip_address'] = (empty($data['ip_address']) || !isset($data['ip_address'])) ? '0' :
                str_replace('\\', '', json_encode($data['ip_address'], JSON_UNESCAPED_UNICODE));
            $data['local'] = (empty($data['local']) || !isset($data['local'])) ? '0' :
                str_replace('\\', '', json_encode($data['local'], JSON_UNESCAPED_UNICODE));
        } else {
            unset($data['type']);
        }
        return DB::table($this->table)->where($field, $op, $value)->update($data);
    }
}

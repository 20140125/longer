<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

/**
 * Class PasswordReset
 * @author <fl140125@gmail.com>
 * @package App\Models
 */
class PasswordReset extends Model
{
    /**
     * @var string $table
     */
    public $table = 'os_password_resets';
    /**
     * @var static $instance
     */
    private static $instance;

    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    /**
     * @return PasswordReset
     */
    static public function getInstance()
    {
        if (!self::$instance instanceof self){
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * TODO: 查询一条记录
     * @param string $field
     * @param string $value
     * @param string $op
     * @param array $column
     * @return Model|Builder|null|object
     */
    public function getResult(string $field, string $value,string $op='=', array $column = ['*'])
    {
        return DB::table($this->table)->where($field,$op,$value)->first($column);
    }

    /**
     * TODO: 添加记录
     * @param array $data
     * @return bool
     */
    public function addResult(array $data)
    {
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
    public function updateResult(array $data,string $field,int $value,$op='=')
    {
        return DB::table($this->table)->where($field,$op,$value)->update($data);
    }

    /**
     * TODO: 删除一条数据
     * @param string $field
     * @param int $value
     * @param string $op
     * @return int
     */
    public function deleteResult(string $field,int $value,string $op='=')
    {
        return DB::table($this->table)->where($field,$op,$value)->delete();
    }
}

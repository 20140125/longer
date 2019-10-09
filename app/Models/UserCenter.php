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
    static public function getInstance()
    {
        if (!self::$instance instanceof self){
            return self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * TODO:查询一条记录
     * @param $field
     * @param $value
     * @param string $op
     * @param array $column
     * @return Model|Builder|null|object
     */
    public function getResult($field, $value='',$op='=', $column = ['*'])
    {
        $result = DB::table($this->table)->where($field,$op,$value)->first($column);
        return $result;
    }

    /**
     * TODO:  添加记录
     * @param $data
     * @return bool
     */
    public function addResult($data)
    {
        $data['tags'] = empty($data['tags']) ? '0' : str_replace('\\','',json_encode($data['tags'],JSON_UNESCAPED_UNICODE));
        $data['ip_address'] = empty($data['ip_address']) ? '0' : str_replace('\\','',json_encode($data['ip_address'],JSON_UNESCAPED_UNICODE));
        $data['local'] = empty($data['local']) ? '0' : str_replace('\\','',json_encode($data['local'],JSON_UNESCAPED_UNICODE));
        $result = DB::table($this->table)->insertGetId($data);
        return $result;
    }
    /**
     * TODO:更新一条数据
     * @param $data
     * @param $field
     * @param $value
     * @param string $op
     * @return int
     */
    public function updateResult($data,$field,$value=null,$op='=')
    {
        if (empty($data['type'])) {
            $data['tags'] = empty($data['tags']) ? '0' : str_replace('\\','',json_encode($data['tags'],JSON_UNESCAPED_UNICODE));
            $data['ip_address'] = empty($data['ip_address']) ? '0' : str_replace('\\','',json_encode($data['ip_address'],JSON_UNESCAPED_UNICODE));
            $data['local'] = empty($data['local']) ? '0' : str_replace('\\','',json_encode($data['local'],JSON_UNESCAPED_UNICODE));
        } else {
            unset($data['type']);
        }
        $result = DB::table($this->table)->where($field,$op,$value)->update($data);
        return $result;
    }
}

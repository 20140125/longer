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
     * @param string|int $value
     * @param string $op
     * @param array $column
     * @return Model|Builder|object|null
     */
    public function getResult(string $field, $value,string $op='=', array $column = ['*'])
    {
        return DB::table($this->table)->where($field,$op,$value)->first($column);
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
     * @param array $data
     * @param string $field
     * @param int $value
     * @param string $op
     * @return int
     */
    public function updateResult(array $data,string $field,int $value,string $op='=')
    {
        $data['updated_at'] = time();
        $data['created_at'] = empty($data['created_at']) ? time() : strtotime($data['created_at']);
        if (!empty($data['value']) && is_array($data['value'])) {
            $data['created_at'] = strtotime($data['created_at']);
            $data['value']['created_at'] = date('Y-m-d H:i:s');
            $data['value']['updated_at'] = date('Y-m-d H:i:s');
            $res = $this->getResult($field,$value);
            $ids = [];
            if ($res->value && count(json_decode($res->value,true))>0) {
                $configVal = json_decode($res->value,true);
                foreach ($configVal as $item) {
                    array_push($ids,$item['id']);
                }
                $data['value']['id'] = (int)max($ids)+1;
                $data['value']['pid'] = $res->id;
                array_push($configVal,$data['value']);
            } else {
                $data['value']['id'] = $res->id*100;
                $data['value']['pid'] = $res->id;
                $configVal = [$data['value']];
            }
            $data['value'] = str_replace('\\','',json_encode($configVal,JSON_UNESCAPED_UNICODE));
        }
        unset($data['children']);
        unset($data['act']);
        return DB::table($this->table)->where($field,$op,$value)->update($data);
    }

    /**
     * TODO：修改配置值
     * @param array $data
     * @param string $field
     * @param int $value
     * @return int
     */
    public function updateValResult(array $data,string $field,int $value)
    {
        $config = $this->getResult($field,$value);
        $configVal = json_decode($config->value,true);
        $data['updated_at'] = date('Y-m-d H:i:s');
        $configObj = $data;
        $configObj['status'] = (int)$configObj['status'];
        $configArr = [];
        foreach ($configVal as $item) {
            if ($item['id'] == $data['id']) {
                if ($data['act'] === 'update') {
                    unset($configObj['act']);
                    array_push($configArr,$configObj);
                } else {
                    unset($item);
                }
            } else {
                array_push($configArr,$item);
            }
        }
        $res['value'] = str_replace('\\','',json_encode($configArr,JSON_UNESCAPED_UNICODE));
        return DB::table($this->table)->where($field,$value)->update($res);
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

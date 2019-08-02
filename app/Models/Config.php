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
     * @param $field
     * @param $value
     * @param string $op
     * @param array $column
     * @return Model|Builder|null|object
     */
    public function getResult($field, $value,$op='=', $column = ['*'])
    {
        $result = DB::table($this->table)->where($field,$op,$value)->first($column);
        return $result;
    }

    /**
     * TODO：获取配置列表
     * @return mixed
     */
    public function getResultLists()
    {
        $result = DB::table($this->table)->get();
        return $result;
    }

    /**
     * TODO: 添加记录
     * @param $data
     * @return bool
     */
    public function addResult($data)
    {
        $data['created_at'] = $data['updated_at'] = time();
        $result = DB::table($this->table)->insertGetId($data);
        return $result;
    }

    /**
     * TODO: 更新一条数据
     * @param $data
     * @param $field
     * @param $value
     * @param string $op
     * @return int
     */
    public function updateResult($data,$field,$value,$op='=')
    {
        $data['updated_at'] = time();
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
            $data['value'] = str_replace('\\','',json_encode($configVal));
        }
        unset($data['children']);
        unset($data['act']);
        $result = DB::table($this->table)->where($field,$op,$value)->update($data);
        return $result;
    }

    /**
     * TODO：修改配置值
     * @param $data
     * @param $field
     * @param $value
     * @return int
     */
    public function updateValResult($data,$field,$value)
    {
        $config = $this->getResult($field,$value);
        $configVal = json_decode($config->value,true);
        $data['updated_at'] = date('Y-m-d H:i:s');
        $configObj = $data;
        $configArr = [];
        foreach ($configVal as $item) {
            if ($item['pid'] == $value) {
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
        $res['value'] = str_replace('\\','',json_encode($configArr));
        $result = DB::table($this->table)->where($field,$value)->update($res);
        return $result;
    }

    /**
     * TODO: 删除一条数据
     * @param $field
     * @param $value
     * @param string $op
     * @return int
     */
    public function deleteResult($field,$value,$op='=')
    {
        $result = DB::table($this->table)->where($field,$op,$value)->delete();
        return $result;
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class Area
 * @author <fl140125@gmail.com>
 * @package App\Models
 */
class Area extends Model
{
    public $table = 'os_china_area';
    /**
     * @var static $instance
     */
    protected static $instance;

    /**
     * @return Area
     */
    static public function getInstance()
    {
        if (!self::$instance instanceof self){
            return self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * TODO:获取数据
     * @param $filed
     * @param $value
     * @param $op
     * @param array $columns
     * @return Model|Builder|object|null
     */
    public function getResult($filed,$value,$op='=',$columns=['*'])
    {
        return DB::table($this->table)->where($filed,$op,$value)->first($columns);
    }

    /**
     * TODO:获取所有记录
     * @param int $pid
     * @param array $columns
     * @return Collection
     */
    public function getResultLists($pid = 1,$columns=['*'])
    {
        $result = DB::table($this->table)->where('parent_id',$pid)->get($columns);
        return $result;
    }

    /**
     * TODO:更新数据
     * @param $data
     * @param $filed
     * @param $value
     * @param string $op
     * @return int
     */
    public function updateResult($data,$filed,$value,$op='=')
    {
        return DB::table($this->table)->where($filed,$op,$value)->update($data);
    }
}

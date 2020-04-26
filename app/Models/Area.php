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
     * @param string $filed
     * @param int $value
     * @param string $op
     * @param array $columns
     * @return Model|Builder|object|null
     */
    public function getResult(string $filed,int $value,string $op='=',array $columns=['*'])
    {
        return DB::table($this->table)->where($filed,$op,$value)->first($columns);
    }

    /**
     * TODO:获取所有记录
     * @param int $pid
     * @param array $columns
     * @return Collection
     */
    public function getResultLists(int $pid = 1,array $columns=['*'])
    {
        return DB::table($this->table)->where('parent_id',$pid)->get($columns);
    }

    /**
     * TODO:获取所有记录
     * @param array $columns
     * @return Collection
     */
    public function getAll(array $columns=['*'])
    {
        return DB::table($this->table)->get($columns);
    }

    /**
     * TODO:获取城市分组
     * @return Collection
     */
    public function getListsGroupByParentId()
    {
        return DB::table($this->table)->where('parent_id','>',0)->groupBy(['parent_id'])->get(['parent_id','id']);

    }

    /**
     * TODO:更新数据
     * @param array $data
     * @param string $filed
     * @param int $value
     * @param string $op
     * @return int
     */
    public function updateResult(array $data,string $filed,int $value,string $op='=')
    {
        return DB::table($this->table)->where($filed,$op,$value)->update($data);
    }
}

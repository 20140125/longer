<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class AuthModel
 * @package App\Model
 */
class AuthModel extends Model
{
    /**
     * @var string $table
     */
    protected $table = 'os_auth_rules';
    /**
     * @var static $instance
     */
    private static $instance;
    /**
     * @return AuthModel
     */
    static public function getInstance()
    {
        if (!self::$instance instanceof self){
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * 权限列表
     * @param int $pid
     * @param array $ids
     * @return Collection
     */
    public function getMenuLists($pid = 0,$ids=[])
    {
        $where[] = array('pid','=',$pid);
        $where[] = array('status','=',1);;
        if (empty($ids)){
            $result = DB::table($this->table)->where($where)->get();
            foreach ($result as &$item){
                $item->__children = $this->getMenuLists($item->id);
            }
            return $result;
        }
        $result = DB::table($this->table)->where($where)->whereIn('id',$ids)->get();
        foreach ($result as &$item){
            $item->__children = $this->getMenuLists($item->id,$ids);
        }
        return $result;
    }
}

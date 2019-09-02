<?php

namespace App\Models;

use App\Http\Controllers\Utils\Code;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class Users
 * @author <fl140125@gmail.com>
 * @package App\Models
 */
class Users extends Model
{
    /**
     * @var string $table
     */
    public $table = 'os_users';
    /**
     * @var static $instance
     */
    private static $instance;
    /**
     * @var static $code
     */
    protected static $code;

    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    /**
     * @return Users
     */
    static public function getInstance()
    {
        if (!self::$instance instanceof self){
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * TODO:  登陆后台
     * @param $data
     * @return array|int
     */
    public function loginRes($data)
    {
        $result = $this->getResult( 'username',$data['username']);
        if (empty($result)){
            return Code::ERROR;
        }
        $password = md5 (md5($data['password']).$result->salt);
        if ($password!==$result->password){
            return Code::ERROR;
        }
        if ($result->status == 2){
            return Code::NOT_ALLOW;
        }
        $request = array('ip_address' =>request()->ip(), 'updated_at' =>time());
        $request['salt'] = get_round_num(8);
        $request['password'] = md5 (md5($data['password']).$request['salt']);
        $request['remember_token'] = md5 (md5($request['password']).$request['salt']);
        $this->updateResult($request,'id',$result->id);
        $admin['token'] = $request['remember_token'];
        $admin['username'] = $result->username;
        return $admin;
    }

    /**
     * TODO:  获取管理员列表
     * @return Collection
     */
    public function  getResultList()
    {
        $result = DB::table($this->table)->join('os_role',$this->table.'.role_id','=','os_role.id')
            ->get(['os_users.*','os_role.id as role_id']);
        return $result;
    }


    /**
     * TODO:  查询一条记录
     * @param $field
     * @param string $value
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
        $result = DB::table($this->table)->insertGetId($data);
        return $result;
    }

    /**
     * TODO:  更新一条数据
     * @param $data
     * @param $field
     * @param $value
     * @param $op
     * @return int
     */
    public function updateResult($data,$field,$value,$op='=')
    {
        $result = DB::table($this->table)->where($field,$value,$op)->update($data);
        return $result;
    }

    /**
     * TODO:  删除一条数据
     * @param $field
     * @param $value
     * @param $op
     * @return int
     */
    public function deleteResult($field,$value,$op='=')
    {
        $result = DB::table($this->table)->where($field,$value,$op)->delete();
        return $result;
    }
}

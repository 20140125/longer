<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Utils\Amap;
use App\Http\Controllers\Utils\Code;
use App\Http\Controllers\Utils\RedisClient;
use App\Mail\Login;
use App\Models\UserCenter;
use App\Models\Users;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * TODO: 通用
 * Class CommonController
 * @author <fl140125@gmail.com>
 * @package App\Http\Controllers\Api
 */
class CommonController
{
    /**
     * @var RedisClient $redisUtils
     */
    protected $redisUtils;
    /**
     * @var Users $usersModel
     */
    protected $usersModel;

    protected static $instance;

    /**
     * @return static
     */
    public static function getInstance ()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * CommonController constructor.
     */
    public function __construct()
    {
        $this->usersModel = Users::getInstance();
        $this->redisUtils = RedisClient::getInstance();
    }
    /**
     * TODO:更新用户画像
     * @return int
     */
    public function updateUserAvatarUrl()
    {
        $users = $this->usersModel->getAll([],['username as client_name','avatar_url as client_img','uuid as uid','id']);
        foreach ($users as &$user) {
            $user->desc = UserCenter::getInstance()->getResult('uid',$user->id,'=',['desc'])->desc;
            $user->id = md5($user->id).uniqid();
        }
        if ($this->redisUtils->sMembers(config('app.chat_user_key'))) {
            $this->redisUtils->del(config('app.chat_user_key'));
        }
        //同步画像重新同步uuid（方便）
        Artisan::call('longer:sync_client_id');
        return $this->redisUtils->sAdd(config('app.chat_user_key'),json_encode($users,JSON_UNESCAPED_UNICODE));
    }

    /**
     * todo:获取当前城市code
     * @return bool|mixed|string
     */
    public function getCityCode ()
    {
        try {
            $address = object_to_array(Amap::getInstance()->getAddress(request()->ip() == '127.0.0.1' ? get_server_ip() : request()->ip()));
            return ($address['adcode'] && $address['adcode']!=='[ ]') ? $address['adcode'] : '110000';
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return false;
        }
    }

    /**
     * todo:邮件发送
     * @param $post
     * @return bool|Model|Builder|int|object|null
     */
    public function sendMail($post)
    {
        $post['verify_code'] = get_round_num(8,'number');
        //验证码保存到redis，10分钟有效
        $this->redisUtils->setValue($post['email'],$post['verify_code'],['EX'=>600]);
        try{
            Mail::to($post['email'])->send(new Login($post));
            if (!Mail::failures()) {
                $data = array(
                    'email' => $post['email'],
                    'code'  => $post['verify_code'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                );
                return $this->verifyMailAndCode($post,$data);
            }
            return false;
        }catch (\Exception $exception){
            Log::error($exception->getMessage());
            return false;
        }
    }
    /**
     * TODO:邮箱和邮箱验证码验证
     * @param $post
     * @param $data
     * @return bool|Model|Builder|int|object|null
     */
    public function verifyMailAndCode($post,$data)
    {
        $result = DB::table('os_send_email')->where(['email'=>$post['email']])->where('updated_at','>=',date('Y-m-d H:i:s',strtotime('-10 minutes')))->first();
        if (!empty($result)) {
            unset($data['created_at']);
            $result = DB::table('os_send_email')->where(['code'=>$result->code,'email'=>$post['email']])->update($data);
        } else {
            $result = DB::table('os_send_email')->insert($data);
        }
        return $result;
    }

    /**
     * TODO：推送站内信息处理
     * @param $post
     * @return string
     */
    public function pushMessage($post)
    {
        $post['state'] = Code::WebSocketState[1];
        $post['created_at'] = time();
        $post['uid'] = $post['username'];
        try{
            //推送给个人
            if ($this->redisUtils->sIsMember(config('app.redis_user_key'),$post['uid'])) {
                if (web_push($post['info'],$post['uid'])) {
                    $post['state'] = Code::WebSocketState[0];
                    return $post['state'];
                }
                return $post['state'];
            }
            $post['state'] = Code::WebSocketState[2];
            return $post['state'];
        }catch (\Exception $e){
            Log::error("站内信息推送失败：".$e->getMessage());
            return $post['state'];
        }
    }
}

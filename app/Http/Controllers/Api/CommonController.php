<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Utils\Amap;
use App\Http\Controllers\Utils\RedisClient;
use App\Models\Area;
use App\Models\Users;
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
        $this->redisUtils = new RedisClient();
    }
    /**
     * TODO:更新用户画像
     * @return int
     */
    public function updateUserAvatarUrl()
    {
        $users = $this->usersModel->getAll([],['username as client_name','avatar_url as client_img','uuid as uid']);
        if ($this->redisUtils->sMembers(config('app.chat_user_key'))) {
            $this->redisUtils->del(config('app.chat_user_key'));
        }
        return $this->redisUtils->sAdd(config('app.chat_user_key'),json_encode($users,JSON_UNESCAPED_UNICODE));
    }
    /**
     * todo:获取当前城市code
     * @return mixed|string
     */
    public function getCityCode ()
    {
        $address = Amap::getInstance()->getAddress(get_server_ip());
        return ($address['adcode'] && $address['adcode']!=='[ ]') ? $address['adcode'] : '110000';
    }
}

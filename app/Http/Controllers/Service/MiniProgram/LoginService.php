<?php

namespace App\Http\Controllers\Service\MiniProgram;

use App\Http\Controllers\Service\v1\UserService;
use App\Http\Controllers\Utils\Code;
use App\Mail\Register;
use Curl\Curl;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;

class LoginService extends BaseService
{
    /**
     * @var static $instance
     */
    private static $instance;

    /**
     * @return LoginService
     */
    public static function getInstance(): LoginService
    {
        if (!self::$instance instanceof self) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    /**
     * todo:获取openID
     * @param $form
     * @return array
     */
    public function getOpenId($form): array
    {
        $url = 'https://api.weixin.qq.com/sns/jscode2session?';
        $data = array(
            'appid'      => $this->appid,
            'secret'     => $this->appSecret,
            'js_code'    => $form['code'],
            'grant_type' => 'authorization_code'
        );
        $curl = new Curl();
        $response = $curl->post($url . http_build_query($data));
        $this->return['lists'] = json_decode(trim($response), true, 512, JSON_OBJECT_AS_ARRAY);
        return $this->return;
    }

    /**
     * todo:小程序登录
     * @param $form
     * @return array
     */
    public function wxLogin($form): array
    {
        try {
            $oauth = [
                'username'       => $form['nickName'],
                'openid'         => $form['code2Session']['openid'] ?? 0,
                'access_token'   => $form['code2Session']['session_key'] ?? 0,
                'expires'        => time() + intval($form['code2Session']['expires_in']) ?? 0,
                'role_id'        => 2,
                'created_at'     => time(),
                'updated_at'     => time(),
                'remember_token' => encrypt(md5($form['nickName']) . time()),
                'oauth_type'     => 'weixin',
                'avatar_url'     => $form['avatarUrl'] ?? UserService::getInstance()->getUserAvatarImage()
            ];
            $where[] = array('openid', '=', $oauth['openid']);
            $where[] = array('oauth_type', '=', 'weixin');
            $result = $this->oauthModel->getOne($where);
            if (!empty($result)) {
                $result->remember_token = encrypt($oauth['username'] . time());
                $this->oauthModel->updateOne(['id' => $result->id], ['remember_token' => $result->remember_token]);
                /* 缓存用户登录标识（脚本缓存有时间延时） */
                UserService::getInstance()->setVerifyCode($result->remember_token, $result->remember_token, config('app.app_refresh_login_time'));
                Artisan::call("longer:sync-oauth $result->remember_token");
                $this->return['lists'] = $result;
                return $this->return;
            }
            if (!$this->oauthModel->saveOne($oauth)) {
                $this->return['Code'] = Code::ERROR;
                $this->return['message'] = 'Failed login system';
                return $this->return;
            }
            /* 缓存用户登录标识（脚本缓存有时间延时） */
            UserService::getInstance()->setVerifyCode($oauth['remember_token'], $oauth['remember_token'], config('app.app_refresh_login_time'));
            Artisan::call("longer:sync-oauth {$oauth['remember_token']}");
            $this->return['lists'] = $oauth;
            /* 邮件通知 */
            Mail::to(config('app.username'))->send(new Register(array('name' => $oauth['username'])));
            return $this->return;
        } catch (\Exception $exception) {
            $this->return['code'] = Code::SERVER_ERROR;
            $this->return['message'] = $exception->getMessage();
            return $this->return;
        }
    }
}

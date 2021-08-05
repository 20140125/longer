<?php

namespace App\Http\Controllers\Service\MiniProgram;

use App\Http\Controllers\Utils\Code;
use Curl\Curl;
use Illuminate\Support\Facades\Artisan;

class LoginService extends BaseService
{
    /**
     * @var static $instance
     */
    private static $instance;
    /**
     * @return static
     */
    public static function getInstance()
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
    public function getOpenId($form)
    {
        $url = 'https://api.weixin.qq.com/sns/jscode2session?';
        $data = array(
            'appid' =>$this->appid,
            'secret' =>$this->appsecret,
            'js_code' =>$form['code'],
            'grant_type' =>'authorization_code'
        );
        $curl = new Curl();
        $response = $curl->post($url.http_build_query($data));
        $this->return['lists'] = json_decode(trim($response), true, 512, JSON_OBJECT_AS_ARRAY);
        return $this->return;
    }

    /**
     * todo:小程序登录
     * @param $form
     * @return array
     */
    public function wxLogin($form)
    {
        try {
            $oauth = [
                'username' => $form['nickName'],
                'openid' => $form['code2Session']['openid'] ?? 0,
                'access_token' => $form['code2Session']['session_key'] ?? 0,
                'expires' => time() + $form['code2Session']['expires_in'] ?? 0,
                'role_id' => 2,
                'created_at' => time(),
                'updated_at' => time(),
                'remember_token' => encrypt(md5($form['nickName']).time()),
                'oauth_type' => 'weixin',
                'avatar_url' => $form['avatarUrl']
            ];
            $where = [['openid' => $oauth['openid'], ['oauth_type' => 'weixin']]];
            $result = $this->oauthModel->getOne($where);
            if (!empty($result)) {
                if ($result->updated_at + 3600 * 24 > time()) {
                    $result->remember_token = encrypt($result->remember_token);
                    $this->oauthModel->updateOne(['id' => $result->id], (array)$result);
                }
                return $this->return;
            }
            if (!$this->oauthModel->saveOne($oauth)) {
                $this->return['Code'] = Code::ERROR;
                $this->return['message'] = 'Failed login system';
                return $this->return;
            }
            Artisan::call("longer:sync-oauth {$oauth['remember_token']}");
            return $this->return;
        } catch (\Exception $exception) {
            $this->return['code'] = Code::SERVER_ERROR;
            $this->return['message'] = $exception->getMessage();
            return $this->return;
        }
    }
}

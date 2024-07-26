<?php

namespace App\Http\Controllers\Service\v1;

use App\Http\Controllers\Utils\Code;
use App\Mail\Login;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEMailService extends BaseService
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
     * todo:邮件发送
     * @param $form
     * @return array
     */
    public function sendMail($form)
    {
        $form['verify_code'] = getRoundNum(8, 'number');
        //验证码保存到redis，10分钟有效
        $this->setVerifyCode($form['email'], $form['verify_code'], 600);
        try {
            Mail::to($form['email'])->send(new Login($form));
            if (!Mail::failures()) {
                $data = array(
                    'email'      => $form['email'],
                    'code'       => $form['verify_code'],
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                );
                $result = $this->checkVerifyCode($data);
                if (!$result) {
                    $this->return['code'] = Code::ERROR;
                    $this->return['message'] = 'Failed send mail';
                    return $this->return;
                }
                $this->return['lists'] = $form;
                return $this->return;
            }
            return array('code' => Code::ERROR, 'message' => Mail::failures());
        } catch (\Exception $exception) {
            return array('code' => Code::ERROR, 'message' => $exception->getMessage());
        }
    }

    /**
     * todo:校验验证码
     * @param $data
     * @return int
     */
    public function checkVerifyCode($data)
    {
        $where = [];
        $where[] = ['email', $data['email']];
        $where[] = ['updated_at', '>=', date('Y-m-d H:i:s', strtotime('-10 minutes'))];
        $result = $this->sendEMailModel->getOne($where);
        if ($result) {
            return $this->sendEMailModel->updateOne(['code' => $result->code, 'email' => $data['email']], ['code' => $data['code']]);
        }
        return $this->sendEMailModel->saveOne($data);
    }
}

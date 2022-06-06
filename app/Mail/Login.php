<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Login extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var $request
     */
    protected $request;

    /**
     * TODO:Create a new message instance
     * Oauth constructor.
     * @param $request
     */
    public function __construct($request)
    {
        $this->request = $request;
    }

    /**
     * @var string $subject
     */
    public $subject = '邮箱验证码';

    /**
     * Build the message.
     * @return Login
     */
    public function build(): Login
    {
        return $this->view('email.login')->with([
            'code' => $this->request['verify_code'],
            'url'  => config('app.url') . 'login'
        ]);
    }
}

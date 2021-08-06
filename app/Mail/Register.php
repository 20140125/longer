<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Register extends Mailable
{
    use Queueable, SerializesModels;
    /**
     * @var $oauth
     */
    protected $request;
    /**
     * Create a new message instance.
     * @param $request
     * @return void
     */
    public function __construct($request)
    {
        $this->request = $request;
    }

    /**
     * @var string $subject
     */
    public $subject = '新用户注册';

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('email.register')->with(['name'=>$this->request['name']]);
    }
}

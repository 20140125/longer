<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Oauth extends Mailable
{
    use Queueable, SerializesModels;

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
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('email.oauth')->with(['code'=>$this->request['verify_code'],'url'=>config('app.url')]);
    }
}
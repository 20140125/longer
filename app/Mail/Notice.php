<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class Notice extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @var $request
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
    public $subject = '申请权限通知';

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('email.notice')
            ->with([
                'href'      => config('app.url') . str_replace(['/admin/'], ['api/v1/'], $this->request['href']),
                'rule_name' => $this->request['rule_name'],
                'username'  => $this->request['username'],
                'url'       => config('app.url') . "/admin/index/{$this->request['remember_token']}"
            ]);
    }
}

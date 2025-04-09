<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Agent;

class UserLogged extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The user instance.
     *
     * @var  \App\Operator
     */
    public $user;

    /**
     * IP address.
     *
     * @var  string
     */
    public $ip;

    /**
     * Netname.
     *
     * @var  string
     */
    public $netname;

    /**
     * Create a new message instance.
     *
     * @param  \App\Operator  $operator
     * @param  string  $ip
     *
     * @return void
     */
    public function __construct(Agent $operator, $ip, $netname)
    {
        $this->user = $operator;
        $this->ip = $ip;
        $this->netname = $netname;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = sprintf(
            'Успешная авторизация %s (%s) c IP %s (%s)',
            $this->user->username,
            $this->user->name,
            $this->ip,
            $this->netname
        );

        return $this
            ->subject($subject)
            ->from(config('mail.from.address'), config('mail.from.name'))
            ->view('emails.login');
    }
}

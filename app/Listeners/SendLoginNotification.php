<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Http\Request;
use App\Mail\UserLogged;

class SendLoginNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Handle the event.
     *
     * @param  Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        Log::info(sprintf(
            'Успешная авторизация %s %s',
            $this->request->input('username'),
            $this->request->input('password')
        ));

        $ip = $this->request->ip();

        $netname = null;
        exec("whois {$ip} | grep netname", $response);
        if (isset($response[0])) {
            $netname = preg_replace('/netname:\s+/', '', $response[0]);
        }

        /*Mail::to(config('mail.to'))->send(
            new UserLogged($event->user, $ip, $netname)
        );*/
    }
}

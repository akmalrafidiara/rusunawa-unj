<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;

class LogSuccessfulLogin
{
    protected $request;

    /**
     * Create the event listener.
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $user = $event->user;

        $user->activityLogs()->create([
            'activity'   => 'Logged In',
            'url'        => $this->request->fullUrl(),
            'ip_address' => $this->request->ip(),
            'user_agent' => $this->request->header('user-agent'),
        ]);
    }
}

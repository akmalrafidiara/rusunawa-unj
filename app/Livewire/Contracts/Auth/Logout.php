<?php

namespace App\Livewire\Contracts\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Logout
{
    public function __invoke()
    {
        Auth::guard('contract')->logout();

        Session::invalidate();
        Session::regenerateToken();

        return redirect('/');
    }
}

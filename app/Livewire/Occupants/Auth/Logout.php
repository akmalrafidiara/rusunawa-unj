<?php

namespace App\Livewire\Occupants\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Logout
{
    public function __invoke()
    {
        Auth::guard('occupant')->logout();

        Session::invalidate();
        Session::regenerateToken();

        return redirect('/');
    }
}

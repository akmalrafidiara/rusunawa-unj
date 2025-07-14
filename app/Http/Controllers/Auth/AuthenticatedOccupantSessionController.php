<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedOccupantSessionController extends Controller
{
    public function authUrl(Request $request, $data)
    {
        $contract = Contract::findOrFail(decrypt($data));

        if (!$request->hasValidSignature()) {
            abort(401, 'Link tidak valid atau sudah kedaluwarsa.');
        }

        Auth::guard('occupant')->login($contract->pic->first());

        return redirect()->route('occupant.dashboard');
    }

    public function logout(Request $request)
    {
        Auth::guard('occupant')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}

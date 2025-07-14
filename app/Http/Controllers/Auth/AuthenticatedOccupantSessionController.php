<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedOccupantSessionController extends Controller
{
    public function __invoke(Request $request, $data)
    {
        $contract = Contract::findOrFail(decrypt($data));

        if (!$request->hasValidSignature()) {
            abort(401, 'Link tidak valid atau sudah kedaluwarsa.');
        }

        Auth::guard('occupant')->login($contract->pic->first());

        session()->forget('url.intended');

        return redirect()->route('occupant.dashboard');
    }
}

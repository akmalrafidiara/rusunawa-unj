<?php
use App\Http\Controllers\Auth\AuthenticatedOccupantSessionController;
use Illuminate\Support\Facades\Route;

Route::prefix('occupant')->group(function () {

    Route::prefix('auth')->group(function () {
        Route::view('/', 'modules.occupants.auth.index')->name('occupant.auth');

        Route::get('/{data}', [AuthenticatedOccupantSessionController::class, '__invoke'])->name('occupant.auth.url');
    });


    Route::middleware(['auth.occupant'])->group(function () {
        // Rute untuk dashboard penghuni
        Route::get('/', function () {
            // Ambil data dari session
            $contractId = session('occupant_contract_id');
            $occupantId = session('logged_in_occupant_id');

            $contract = \App\Models\Contract::find($contractId);
            $occupant = \App\Models\Occupant::find($occupantId);

            // Kirim data ke view dashboard Anda
            return view('modules.occupants.dashboard.index', compact('contract', 'occupant'));

        })->name('occupant.dashboard');
    });
});

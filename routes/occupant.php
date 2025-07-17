<?php
use App\Http\Controllers\Auth\AuthenticatedOccupantSessionController;
use Illuminate\Support\Facades\Route;

Route::prefix('occupant')->group(function () {

    Route::prefix('auth')->group(function () {
        Route::view('/', 'modules.occupants.auth.index')->name('occupant.auth');

        Route::get('/{data}', [AuthenticatedOccupantSessionController::class, 'authUrl'])->name('occupant.auth.url');
        Route::post('/logout', [AuthenticatedOccupantSessionController::class, 'logout'])->name('occupant.auth.logout');
    });


    Route::middleware(['auth.occupant'])->group(function () {
        // Rute untuk dashboard penghuni
        Route::get('/', function () {
            return view('modules.occupants.dashboard.index');
        })->name('occupant.dashboard');
    });
});

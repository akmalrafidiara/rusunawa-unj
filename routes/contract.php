<?php
use App\Http\Controllers\Auth\AuthenticatedContractSessionController;
use Illuminate\Support\Facades\Route;

Route::prefix('contract')->group(function () {

    Route::prefix('auth')->group(function () {
        Route::view('/', 'modules.contracts.auth.index')->name('contract.auth');

        Route::get('/{data}', [AuthenticatedContractSessionController::class, 'authUrl'])->name('contract.auth.url');
        Route::post('/logout', [AuthenticatedContractSessionController::class, 'logout'])->name('contract.auth.logout');
    });


    Route::middleware(['auth.contract'])->group(function () {
        // Rute untuk dashboard penghuni
        Route::view('/', 'modules.contracts.dashboard.index')->name('contract.dashboard');
    });
});

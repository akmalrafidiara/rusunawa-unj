<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Web
// Route::get('/', function () {
//     return view('welcome');
// })->name('home');
Route::view('/', 'modules.frontend.home')->name('home');

// Managers Dashboard
Route::prefix('managers')->middleware(['auth', 'verified'])->group(function () {

    // Settings
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    // Dashboard
    Route::view('/', 'modules.managers.overview')->name('dashboard');

    // Responses
    Route::view('occupant-verification', 'modules.managers.responses.occupant-verification')->name('occupant.verification');
    Route::view('payment-confirmation', 'modules.managers.responses.payment-confirmation')->name('payment.confirmation');

    // Tenancy
    Route::view('contracts', 'modules.managers.tenancy.contracts')->name('contracts');
    Route::view('invoices', 'modules.managers.tenancy.invoices')->name('invoices');
    Route::view('occupants', 'modules.managers.tenancy.occupants')->name('occupants');

    // Oprations
    Route::view('users', 'modules.managers.oprations.users')->name('users');
    Route::view('incomes-reports', 'modules.managers.oprations.income-reports')->name('income.reports');
    Route::view('reports-and-complaints', 'modules.managers.oprations.reports-and-complaints')->name('reports.and.complaints');
    Route::view('maintenance', 'modules.managers.oprations.maintenance')->name('maintenance');
    Route::prefix('units')->group(function () {
        Route::view('/', 'modules.managers.oprations.units.index')->name('units');
        Route::view('types', 'modules.managers.oprations.units.types')->name('unit.types');
        Route::view('clusters', 'modules.managers.oprations.units.clusters')->name('unit.clusters');
        Route::view('rates', 'modules.managers.oprations.units.rates')->name('unit.rates');
    });

    // Content
});

require __DIR__.'/auth.php';

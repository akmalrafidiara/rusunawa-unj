<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

// Web
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Managers Dashboard
Route::prefix('managers')->middleware(['auth', 'verified'])->group(function () {

    // Settings
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    // Dashboard
    Route::view('/', 'managers.overview')->name('dashboard');

    // Responses
    Route::view('occupant-verification', 'managers.responses.occupant-verification')->name('occupant.verification');
    Route::view('payment-confirmation', 'managers.responses.payment-confirmation')->name('payment.confirmation');

    // Tenancy
    Route::view('contracts', 'managers.tenancy.contracts')->name('contracts');
    Route::view('invoices', 'managers.tenancy.invoices')->name('invoices');
    Route::view('occupants', 'managers.tenancy.occupants')->name('occupants');

    // Oprations
    Route::view('users', 'managers.oprations.users')->name('users');
    Route::view('incomes-reports', 'managers.oprations.income-reports')->name('income.reports');
    Route::view('reports-and-complaints', 'managers.oprations.reports-and-complaints')->name('reports.and.complaints');
    Route::view('maintenance', 'managers.oprations.maintenance')->name('maintenance');
    Route::prefix('units')->group(function () {
        Route::view('/', 'managers.oprations.units.index')->name('units');
        Route::view('types', 'managers.oprations.units.types')->name('unit.types');
        Route::view('clusters', 'managers.oprations.units.clusters')->name('unit.clusters');
        Route::view('rates', 'managers.oprations.units.rates')->name('unit.rates');
    });

    // Content
});

require __DIR__.'/auth.php';

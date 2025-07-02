<?php

use App\Livewire\Frontend\Announcement\ShowAnnouncement;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Enums\RoleUser; // Import RoleUser

// === SCOPE FRONTEND ROUTES ===
require __DIR__.'/frontend.php';

// === SCOPE MANAGERS ROUTES ===
require __DIR__.'/managers.php';

// === SCOPE DASHBOARD BERDASARKAN ROLE ===
// Dashboard Kepala Rusunawa
Route::prefix('head-of-rusunawa')->middleware(['auth', 'verified', 'role:'.RoleUser::HEAD_OF_RUSUNAWA->value])->group(function () {
    // Dashboard
    Route::view('/', 'modules.head-rusunawa.overview')->name('head_of_rusunawa.dashboard');

    // Settings
    Route::redirect('settings', 'head-of-rusunawa.settings.profile');

    Volt::route('settings/profile', 'head-rusunawa.settings.profile')->name('head-of-rusunawa.settings.profile');
    Volt::route('settings/password', 'head-rusunawa.settings.password')->name('head-of-rusunawa.settings.password');
    Volt::route('settings/appearance', 'head-rusunawa.settings.appearance')->name('head-of-rusunawa.settings.appearance');
});

// Dashboard Staff Rusunawa
Route::prefix('staff-of-rusunawa')->middleware(['auth', 'verified', 'role:'.RoleUser::STAFF_OF_RUSUNAWA->value])->group(function () {
    //Dashboard
    Route::view('/', 'modules.staff-rusunawa.overview')->name('staff_of_rusunawa.dashboard');

    // Settings
    Route::redirect('settings', 'staff-of-rusunawa.settings.profile');

    Volt::route('settings/profile', 'staff-rusunawa.settings.profile')->name('staff-of-rusunawa.settings.profile');
    Volt::route('settings/password', 'staff-rusunawa.settings.password')->name('staff-of-rusunawa.settings.password');
    Volt::route('settings/appearance', 'staff-rusunawa.settings.appearance')->name('staff-of-rusunawa.settings.appearance');
});
require __DIR__.'/auth.php';

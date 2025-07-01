<?php

use App\Livewire\Frontend\Announcement\ShowAnnouncement;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Enums\RoleUser; // Import RoleUser

// === SCOPE FRONTEND ROUTES ===
Route::view('/', 'modules.frontend.home')->name('home');

Route::view('tenancy', 'modules.frontend.tenancy.index')->name('tenancy.index');
Route::view('tenancy/unit-detail', 'modules.frontend.tenancy.unit-detail')->name('frontend.tenancy.unit.detail');

Route::redirect('complaint', 'complaint/track-complaint');
    Volt::route('complaint/track-complaint', 'frontend.complaint.track-complaint')->name('complaint.track-complaint');
    Volt::route('complaint/create-complaint', 'frontend.complaint.create-complaint')->name('complaint.create-complaint');
    Volt::route('complaint/ongoing-complaint', 'frontend.complaint.ongoing-complaint')->name('complaint.ongoing-complaint');
    Volt::route('complaint/complaint-history', 'frontend.complaint.complaint-history')->name('complaint.complaint-history');

Route::view('announcement', 'modules.frontend.announcement.index')->name('announcement.index');
Route::get('announcement/{announcement}', ShowAnnouncement::class)->name('announcement.show');

Route::view('rules', 'modules.frontend.rules.index')->name('rules.index');

// === SCOPE MANAGERS ROUTES ===
// Managers Dashboard (untuk Admin / Kepala BPU)
Route::prefix('managers')->middleware(['auth', 'verified', 'role:'.RoleUser::ADMIN->value])->group(function () {
    // Dashboard
    Route::view('/', 'modules.managers.overview')->name('dashboard');

    // Settings
    Route::redirect('settings', 'managers.settings.profile');

    Volt::route('settings/profile', 'managers.settings.profile')->name('managers.settings.profile');
    Volt::route('settings/password', 'managers.settings.password')->name('managers.settings.password');
    Volt::route('settings/appearance', 'managers.settings.appearance')->name('managers.settings.appearance');

    // Responses
    Route::view('occupant-verification', 'modules.managers.responses.occupant-verification')->name('occupant.verification');
    Route::view('payment-confirmation', 'modules.managers.responses.payment-confirmation')->name('payment.confirmation');

    // Tenancy
    Route::view('contracts', 'modules.managers.tenancy.contracts')->name('contracts');
    Route::view('invoices', 'modules.managers.tenancy.invoices')->name('invoices');
    Route::view('occupants', 'modules.managers.tenancy.occupants')->name('occupants');

    // Oprations
    Route::view('users', 'modules.managers.oprations.users.index')->name('users');
    Route::view('incomes-reports', 'modules.managers.oprations.income-reports.index')->name('income.reports');
    Route::view('reports-and-complaints', 'modules.managers.oprations.reports-and-complaints.index')->name('reports.and.complaints');
    Route::view('maintenance', 'modules.managers.oprations.maintenance.index')->name('maintenance');
    Route::prefix('units')->group(function () {
        Route::view('/', 'modules.managers.oprations.units.index')->name('units');
        Route::view('types', 'modules.managers.oprations.unit-types.index')->name('unit.types');
        Route::view('clusters', 'modules.managers.oprations.unit-clusters.index')->name('unit.clusters');
        Route::view('occupant-types', 'modules.managers.oprations.occupant-types.index')->name('occupant.types');
    });

    // Content
    Route::prefix('page-contents')->group(function () {
        Route::view('banner-footers', 'modules.managers.contents.banner-footer.index')->name('page-contents.banner-footer');
        Route::view('abouts', 'modules.managers.contents.abouts.index')->name('page-contents.abouts');
        Route::view('locations', 'modules.managers.contents.locations.index')->name('page-contents.locations');
        Route::view('galleries', 'modules.managers.contents.galleries.index')->name('page-contents.galleries');
        Route::view('complaint-content', 'modules.managers.contents.complaint-page-content.index')->name('page-contents.complaint-content');
        Route::view('faqs', 'modules.managers.contents.faq.index')->name('page-contents.faq');
        Route::view('contacts', 'modules.managers.contents.contacts.index')->name('page-contents.contacts');
    });
    Route::view(('guest-questions'), 'modules.managers.contents.guest-questions.index')->name('guest.questions');
    Route::view(('announcements'), 'modules.managers.contents.announcements.index')->name('announcements');
    Route::view('regulations', 'modules.managers.contents.regulations.index')->name('regulations');
    Route::view('emergency-contacts', 'modules.managers.contents.emergency-contacts.index')->name('emergency.contacts');
});

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
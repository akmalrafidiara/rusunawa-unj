<?php
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;
use App\Enums\RoleUser;

// Managers Dashboard (untuk Admin / Kepala BPU)
Route::prefix('managers')->middleware(['auth', 'verified', 'role:'.RoleUser::ADMIN->value.'|'.RoleUser::HEAD_OF_RUSUNAWA->value.'|'.RoleUser::STAFF_OF_RUSUNAWA->value])->group(function () {
    // Dashboard
    Route::view('/', 'modules.managers.overview')->name('dashboard');

    // Settings
    Route::redirect('settings', 'managers.settings.profile');

    Volt::route('settings/profile', 'managers.settings.profile')->name('managers.settings.profile');
    Volt::route('settings/password', 'managers.settings.password')->name('managers.settings.password');
    Volt::route('settings/appearance', 'managers.settings.appearance')->name('managers.settings.appearance');

    //Tenancy
    Route::view('contracts', 'modules.managers.tenancy.contracts.index')->name('contracts');
    Route::view('invoices', 'modules.managers.tenancy.invoices.index')->name('invoices');
    Route::view('occupants', 'modules.managers.tenancy.occupants.index')->name('occupants');

    // Complaints
    Route::view('reports-and-complaints', 'modules.managers.oprations.reports-and-complaints.index')->name('reports.and.complaints');

    // Role: ADMIN, Kepala Rusunawa
    Route::middleware(['role:'.RoleUser::ADMIN->value.'|'.RoleUser::HEAD_OF_RUSUNAWA->value])->group(function () {
        // Responses
        Route::view('occupant-verification', 'modules.managers.responses.occupant-verification.index')->name('occupant.verification');
        Route::view('payment-confirmation', 'modules.managers.responses.payment-confirmation.index')->name('payment.confirmation');

        // Oprations
        Route::view('users', 'modules.managers.oprations.users.index')->name('users');
        Route::view('incomes-reports', 'modules.managers.oprations.income-reports.index')->name('income.reports');
        Route::view('maintenance', 'modules.managers.oprations.maintenance.index')->name('maintenance');
        Route::prefix('units')->group(function () {
            Route::view('/', 'modules.managers.oprations.units.index')->name('units');
            Route::view('types', 'modules.managers.oprations.unit-types.index')->name('unit.types');
            Route::view('clusters', 'modules.managers.oprations.unit-clusters.index')->name('unit.clusters');
            Route::view('occupant-types', 'modules.managers.oprations.occupant-types.index')->name('occupant.types');
        });
        Route::view('activity-logs', 'modules.managers.oprations.activity-logs.index')->name('activity.logs');

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
});

<?php

use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::view('/', 'modules.frontend.home')->name('home');

Route::view('tenancy', 'modules.frontend.tenancy.index')->name('tenancy.index');
Route::view('tenancy/unit-detail', 'modules.frontend.tenancy.unit-detail')->name('frontend.tenancy.unit.detail');
Route::view('tenancy/form', 'modules.frontend.tenancy.form')->name('frontend.tenancy.form');

Route::redirect('complaint', 'complaint/track-complaint');
    Volt::route('complaint/track-complaint/{unique_id?}', 'frontend.complaint.track-complaint')->name('complaint.track-complaint');
    // Rute pengaduan
    Volt::route('complaint/create-complaint', 'frontend.complaint.create-complaint')->name('complaint.create-complaint');
    Volt::route('complaint/create-complaint/success/{unique_id}', 'frontend.complaint.complaint-success')->name('complaint.success');
    // Rute pengaduan berjalan
    Volt::route('complaint/ongoing-complaint', 'frontend.complaint.ongoing-complaint')->name('complaint.ongoing-complaint');
    Volt::route('complaint/ongoing-complaint/{unique_id}', 'frontend.complaint.show-complaint')->name('complaint.ongoing-detail');
    // Rute riwayat pengaduan
    Volt::route('complaint/complaint-history', 'frontend.complaint.complaint-history')->name('complaint.complaint-history');
    Volt::route('complaint/complaint-history/{unique_id}', 'frontend.complaint.show-complaint')->name('complaint.history-detail');

Route::view('announcement', 'modules.frontend.announcement.index')->name('announcement.index');
Route::view('announcement/{slug}', 'modules.frontend.announcement.announcement-detail')->name('announcement.show');

Route::view('rules', 'modules.frontend.rules.index')->name('rules.index');

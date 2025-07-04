<?php 
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::view('/', 'modules.frontend.home')->name('home');

Route::view('tenancy', 'modules.frontend.tenancy.index')->name('tenancy.index');
Route::view('tenancy/unit-detail', 'modules.frontend.tenancy.unit-detail')->name('frontend.tenancy.unit.detail');
Route::view('tenancy/form', 'modules.frontend.tenancy.form')->name('frontend.tenancy.form');

Route::redirect('complaint', 'complaint/track-complaint');
    Volt::route('complaint/track-complaint', 'frontend.complaint.track-complaint')->name('complaint.track-complaint');
    Volt::route('complaint/create-complaint', 'frontend.complaint.create-complaint')->name('complaint.create-complaint');
    Volt::route('complaint/ongoing-complaint', 'frontend.complaint.ongoing-complaint')->name('complaint.ongoing-complaint');
    Volt::route('complaint/complaint-history', 'frontend.complaint.complaint-history')->name('complaint.complaint-history');

Route::view('announcement', 'modules.frontend.announcement.index')->name('announcement.index');
Route::view('announcement/{slug}', 'modules.frontend.announcement.announcement-detail')->name('announcement.show');

Route::view('rules', 'modules.frontend.rules.index')->name('rules.index');

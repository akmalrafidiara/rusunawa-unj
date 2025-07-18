<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('contracts:cancel-unpaid-initial')->everyFiveSeconds();

Schedule::command('maintenance:check-schedule')->everyFiveSeconds();

Schedule::command('renewals:check-monthly')->dailyAt('01:00');

Schedule::command('invoices:send-reminders')->dailyAt('09:00');

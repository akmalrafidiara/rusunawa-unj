<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('contracts:cancel-initial-invoice-unpaid')->everyTenMinutes();

Schedule::command('maintenance:check-schedule')->everyTenMinutes();

Schedule::command('contract:check-monthly-renewals')->dailyAt('01:00');

Schedule::command('invoices:send-reminders')->dailyAt('09:00');

Schedule::command('invoices:cancel-overdue-renewal')->dailyAt('02:00');

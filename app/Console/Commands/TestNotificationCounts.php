<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Occupant;
use App\Models\Invoice;
use App\Models\Report;
use App\Models\GuestQuestion;
use App\Models\User;
use App\Enums\OccupantStatus;
use App\Enums\InvoiceStatus;
use App\Enums\ReportStatus;

class TestNotificationCounts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:notification-counts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test notification counts for sidebar badges';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing notification counts...');

        // Count pending occupant verifications
        $pendingOccupants = Occupant::where('status', OccupantStatus::PENDING_VERIFICATION->value)->count();

        // Count pending payment confirmations
        $pendingPayments = Invoice::where('status', InvoiceStatus::PENDING_PAYMENT_VERIFICATION->value)->count();

        // Count unresolved reports
        $pendingReports = Report::whereIn('status', [
            ReportStatus::REPORT_RECEIVED->value,
            ReportStatus::IN_PROCESS->value,
            ReportStatus::DISPOSED_TO_ADMIN->value,
            ReportStatus::DISPOSED_TO_RUSUNAWA->value
        ])->count();

        // Count unread guest questions
        $pendingQuestions = GuestQuestion::where('is_read', false)->count();

        // Count users with unread notifications
        $usersWithNotifications = User::whereHas('notifications', function($query) {
            $query->whereNull('read_at');
        })->count();

        $this->table(['Type', 'Count'], [
            ['Pending Occupant Verifications', $pendingOccupants],
            ['Pending Payment Confirmations', $pendingPayments],
            ['Pending Reports', $pendingReports],
            ['Unread Guest Questions', $pendingQuestions],
            ['Users with Unread Notifications', $usersWithNotifications],
        ]);

        $this->info('Test completed!');
    }
}

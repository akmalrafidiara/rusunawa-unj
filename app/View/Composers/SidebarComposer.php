<?php

namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Occupant;
use App\Models\Invoice;
use App\Models\Report;
use App\Models\GuestQuestion;
use App\Enums\OccupantStatus;
use App\Enums\InvoiceStatus;
use App\Enums\ReportStatus;

class SidebarComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        if (Auth::check()) {
            $user = Auth::user();
            
            // Count pending occupant verifications (those pending verification)
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
            
            // Count unread notifications for current user
            $unreadNotifications = $user->unreadNotifications()->count();
            
            $view->with([
                'pendingOccupants' => $pendingOccupants,
                'pendingPayments' => $pendingPayments,
                'pendingReports' => $pendingReports,
                'pendingQuestions' => $pendingQuestions,
                'unreadNotifications' => $unreadNotifications,
            ]);
        }
    }
}

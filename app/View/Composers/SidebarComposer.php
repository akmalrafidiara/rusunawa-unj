<?php

namespace App\View\Composers;

use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Occupant;
use App\Models\Invoice;
use App\Models\Report;
use App\Models\GuestQuestion;
use App\Models\MaintenanceSchedule;
use App\Enums\OccupantStatus;
use App\Enums\InvoiceStatus;
use App\Enums\ReportStatus;
use App\Enums\MaintenanceScheduleStatus;
use App\Enums\RoleUser;
use Carbon\Carbon;

class SidebarComposer
{
    /**
     * Bind data to the view.
     */
    public function compose(View $view): void
    {
        if (Auth::check()) {
            $user = Auth::user();
            $pendingReports = 0;
            $upcomingMaintenance = 0;

            // Count pending occupant verifications (those pending verification)
            $pendingOccupants = Occupant::where('status', OccupantStatus::PENDING_VERIFICATION->value)->count();
            
            // Count pending payment confirmations
            $pendingPayments = Invoice::where('status', InvoiceStatus::PENDING_PAYMENT_VERIFICATION->value)->count();
            
            // Count unread guest questions
            $pendingQuestions = GuestQuestion::where('is_read', false)->count();

            // Logic for pending reports based on roles
            if ($user->hasRole(RoleUser::ADMIN->value)) {
                $pendingReports = Report::where('status', ReportStatus::DISPOSED_TO_ADMIN->value)->count();
            } elseif ($user->hasRole(RoleUser::HEAD_OF_RUSUNAWA->value)) {
                $pendingReports = Report::whereIn('status', [
                    ReportStatus::REPORT_RECEIVED->value,
                    ReportStatus::IN_PROCESS->value,
                    ReportStatus::DISPOSED_TO_ADMIN->value,
                    ReportStatus::DISPOSED_TO_RUSUNAWA->value
                ])->count();
            } elseif ($user->hasRole(RoleUser::STAFF_OF_RUSUNAWA->value)) {
                $userClusterIds = $user->unitClusters->pluck('id')->toArray();
                if (!empty($userClusterIds)) {
                    $pendingReports = Report::whereIn('status', [
                        ReportStatus::REPORT_RECEIVED->value,
                        ReportStatus::IN_PROCESS->value,
                        ReportStatus::DISPOSED_TO_RUSUNAWA->value
                    ])->whereHas('contract.unit.unitCluster', function ($query) use ($userClusterIds) {
                        $query->whereIn('unit_clusters.id', $userClusterIds);
                    })->count();
                } else {
                    $pendingReports = 0; // Staff with no assigned clusters see no reports
                }
            }

            // Logic for upcoming maintenance based on roles
            $now = Carbon::now();
            $sevenDaysFromNow = $now->copy()->addDays(7);

            $maintenanceQuery = MaintenanceSchedule::whereIn('status', [
                    MaintenanceScheduleStatus::SCHEDULED->value,
                    MaintenanceScheduleStatus::UPCOMING->value
                ])
                ->whereBetween('next_due_date', [$now, $sevenDaysFromNow]);

            if ($user->hasRole(RoleUser::STAFF_OF_RUSUNAWA->value)) {
                $userClusterIds = $user->unitClusters->pluck('id')->toArray();
                if (!empty($userClusterIds)) {
                    $maintenanceQuery->whereHas('unit.unitCluster', function ($query) use ($userClusterIds) {
                        $query->whereIn('unit_clusters.id', $userClusterIds);
                    });
                } else {
                    $maintenanceQuery->whereRaw('1 = 0'); // No results if no clusters
                }
            }

            $upcomingMaintenance = $maintenanceQuery->count();
            
            // Count unread notifications for current user
            $unreadNotifications = $user->unreadNotifications()->count();
            
            $view->with([
                'pendingOccupants' => $pendingOccupants,
                'pendingPayments' => $pendingPayments,
                'pendingReports' => $pendingReports,
                'pendingQuestions' => $pendingQuestions,
                'upcomingMaintenance' => $upcomingMaintenance, // New badge
                'unreadNotifications' => $unreadNotifications,
            ]);
        }
    }
}
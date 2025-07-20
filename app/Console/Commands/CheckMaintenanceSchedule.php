<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\MaintenanceSchedule;
use App\Models\User;
use App\Enums\RoleUser;
use App\Notifications\MaintenanceReminderNotification;
use Carbon\Carbon;

class CheckMaintenanceSchedule extends Command
{
    protected $signature = 'maintenance:check-schedule';
    protected $description = 'Check for upcoming and overdue maintenance and send notifications';

    public function handle()
    {
        // Get users to notify
        $adminsAndHeads = User::role([RoleUser::ADMIN->value, RoleUser::HEAD_OF_RUSUNAWA->value])->get();
        if ($adminsAndHeads->isEmpty()) {
            return 1;
        }

        // Get maintenance schedules
        $schedules = MaintenanceSchedule::with('unit')->get();
        if ($schedules->isEmpty()) {
            return 0;
        }

        $notificationsSent = 0;

        foreach ($schedules as $schedule) {
            if (is_null($schedule->next_due_date)) {
                continue;
            }

            $dueDate = Carbon::parse($schedule->next_due_date)->startOfDay();
            $now = Carbon::now()->startOfDay();
            $daysUntilDue = $now->diffInDays($dueDate, false);

            $message = '';
            $shouldNotify = false;

            // Check for upcoming maintenance reminders
            $reminders = [7, 5, 3, 1, 0];
            if (in_array($daysUntilDue, $reminders)) {
                $shouldNotify = true;
                $message = "Pemeliharaan rutin untuk kamar {$schedule->unit->room_number} ({$schedule->unit->unitCluster->name}) dijadwalkan dalam {$daysUntilDue} hari lagi.";
                if ($daysUntilDue == 0) {
                    $message = "Pemeliharaan rutin untuk kamar {$schedule->unit->room_number} ({$schedule->unit->unitCluster->name}) dijadwalkan hari ini.";
                }
            }
            // Check for overdue maintenance reminders
            else {
                $overdueReminders = [-1, -3, -5, -7];
                if (in_array($daysUntilDue, $overdueReminders)) {
                    $shouldNotify = true;
                    $daysPastDue = abs($daysUntilDue);
                    $message = "PERHATIAN: Pemeliharaan rutin untuk kamar {$schedule->unit->room_number} ({$schedule->unit->unitCluster->name}) sudah terlambat {$daysPastDue} hari.";
                }
            }

            // Send notifications if criteria is met
            if ($shouldNotify) {
                foreach ($adminsAndHeads as $user) {
                    if (!$user->notifications()->where('data->maintenance_schedule_id', $schedule->id)->whereDate('created_at', Carbon::today())->exists()) {
                        $user->notify(new MaintenanceReminderNotification($schedule, $message));
                        $notificationsSent++;
                    }
                }
            }
        }

        return 0;
    }
}

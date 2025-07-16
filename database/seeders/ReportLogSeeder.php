<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Report;
use App\Models\ReportLog;
use App\Models\User;
use App\Enums\ReportStatus;
use App\Enums\RoleUser;
use Carbon\Carbon;

class ReportLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $reports = Report::all();
        $staffUsers = User::role(RoleUser::STAFF_OF_RUSUNAWA->value)->get();

        if ($reports->isEmpty() || $staffUsers->isEmpty()) {
            $this->command->info('Tidak ada Laporan atau Staf untuk membuat Log. Lewati ReportsLogSeeder.');
            return;
        }

        foreach ($reports as $report) {
            $handler = $staffUsers->random();

            // Log saat laporan pertama kali dibuat
            ReportLog::create([
                'report_id' => $report->id,
                'user_id' => $handler->id,
                'action_by_role' => RoleUser::STAFF_OF_RUSUNAWA->value,
                'old_status' => null,
                'new_status' => ReportStatus::REPORT_RECEIVED->value,
                'notes' => 'Laporan diterima dan akan segera diproses oleh staf.',
                'created_at' => $report->created_at,
                'updated_at' => $report->created_at,
            ]);

            // Simulasi log tambahan
            if ($report->id % 2 == 0) { // Hanya untuk beberapa laporan
                ReportLog::create([
                    'report_id' => $report->id,
                    'user_id' => $handler->id,
                    'action_by_role' => RoleUser::STAFF_OF_RUSUNAWA->value,
                    'old_status' => ReportStatus::REPORT_RECEIVED->value,
                    'new_status' => ReportStatus::IN_PROCESS->value,
                    'notes' => 'Laporan sedang dalam penanganan oleh teknisi.',
                    'created_at' => $report->created_at->addHours(1),
                    'updated_at' => $report->created_at->addHours(1),
                ]);
                $report->update(['status' => ReportStatus::IN_PROCESS]);
            }
        }
    }
}
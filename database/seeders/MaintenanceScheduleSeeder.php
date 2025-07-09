<?php

namespace Database\Seeders;

use App\Models\MaintenanceSchedule;
use App\Models\Unit;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Enums\MaintenanceScheduleStatus;

class MaintenanceScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan ada unit di database yang membutuhkan pemeliharaan rutin
        $units = Unit::whereHas('unitType', fn($q) => $q->where('requires_maintenance', true))->get();

        if ($units->isEmpty()) {
            $this->command->info('Tidak ada unit yang membutuhkan pemeliharaan rutin ditemukan. Silakan pastikan UnitSeeder dan UnitTypeSeeder berjalan dengan benar.');
            return;
        }

        // Contoh jadwal rutin untuk beberapa unit yang membutuhkan pemeliharaan
        foreach ($units as $index => $unit) {
            // Cek apakah unit sudah memiliki jadwal
            if ($unit->maintenanceSchedule()->exists()) {
                $this->command->info("Unit Kamar {$unit->room_number} sudah memiliki jadwal pemeliharaan, melompati.");
                continue;
            }

            $frequency = 3; // Setiap 3 bulan sekali
            $nextDueDate = Carbon::now()->addMonths($frequency);

            MaintenanceSchedule::create([
                'unit_id' => $unit->id,
                'frequency_months' => $frequency,
                'next_due_date' => $nextDueDate,
                'last_completed_at' => null, // Harus NULL saat pertama kali dibuat
                'status' => MaintenanceScheduleStatus::SCHEDULED,
                'notes' => 'Jadwal pemeliharaan rutin AC untuk Kamar ' . $unit->room_number,
            ]);

            $this->command->info("Jadwal pemeliharaan rutin dibuat untuk Unit: Kamar {$unit->room_number}");

            // Batasi agar tidak membuat terlalu banyak jadwal jika unit sangat banyak
            if ($index >= 4) break; // Hanya buat untuk 5 unit pertama
        }

        // Contoh jadwal yang mungkin sudah lewat/overdue
        $unitForOverdue = Unit::whereHas('unitType', fn($q) => $q->where('requires_maintenance', true))->inRandomOrder()->first();
        if ($unitForOverdue && !$unitForOverdue->maintenanceSchedule()->exists()) {
            MaintenanceSchedule::create([
                'unit_id' => $unitForOverdue->id,
                'frequency_months' => 6,
                'next_due_date' => Carbon::now()->subDays(15), // 15 hari yang lalu
                'last_completed_at' => null, // Awalnya NULL, akan terisi saat ada rekaman selesai
                'status' => MaintenanceScheduleStatus::OVERDUE, // Status menjadi OVERDUE karena next_due_date di masa lalu
                'notes' => 'Jadwal rutin AC yang sudah terlambat untuk Kamar ' . $unitForOverdue->room_number,
            ]);
            $this->command->info("Jadwal pemeliharaan terlambat dibuat untuk Unit: Kamar {$unitForOverdue->room_number}");
        }
    }
}
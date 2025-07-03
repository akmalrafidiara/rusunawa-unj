<?php

namespace Database\Seeders;

use App\Models\MaintenanceRecord;
use App\Models\MaintenanceSchedule;
use App\Models\Unit;
use App\Models\Attachment;
use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Enums\MaintenanceRecordType;
use App\Enums\MaintenanceRecordStatus;
use App\Enums\MaintenanceScheduleStatus;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\File;
use Illuminate\Support\Str;

class MaintenanceRecordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = Unit::whereHas('unitType', fn($q) => $q->where('requires_maintenance', true))->get(); // Hanya unit yang membutuhkan pemeliharaan
        if ($units->isEmpty()) {
            $this->command->info('Tidak ada unit yang membutuhkan pemeliharaan ditemukan. Lewati MaintenanceRecordSeeder.');
            return;
        }

        // Pastikan direktori penyimpanan ada
        Storage::disk('public')->makeDirectory('maintenance_attachments/images');
        Storage::disk('public')->makeDirectory('maintenance_attachments/files');
        Storage::disk('public')->makeDirectory('temp'); // Direktori sementara untuk dummy file

        // === Contoh Rekaman Rutin ===
        // Ambil beberapa jadwal rutin yang sudah ada untuk unit yang membutuhkan pemeliharaan
        $schedules = MaintenanceSchedule::whereIn('unit_id', $units->pluck('id'))->take(2)->get();
        foreach ($schedules as $schedule) {
            $completionDate = Carbon::parse($schedule->next_due_date)->subDays(rand(0, 5)); // Selesai beberapa hari sebelum/tepat waktu
            $isLate = $completionDate->greaterThan(Carbon::parse($schedule->next_due_date)->startOfDay());
            $isEarly = $completionDate->lt(Carbon::parse($schedule->next_due_date)->startOfDay());

            $recordStatus = MaintenanceRecordStatus::COMPLETED_ON_TIME;
            if ($isEarly) {
                $recordStatus = MaintenanceRecordStatus::COMPLETED_EARLY;
            } elseif ($isLate) {
                $recordStatus = MaintenanceRecordStatus::COMPLETED_LATE;
            }

            $record = MaintenanceRecord::create([
                'unit_id' => $schedule->unit->id,
                'maintenance_schedule_id' => $schedule->id,
                'type' => MaintenanceRecordType::ROUTINE,
                'scheduled_date' => $schedule->next_due_date,
                'completion_date' => $completionDate,
                'status' => $recordStatus,
                'notes' => 'Pemeliharaan rutin AC selesai pada ' . $completionDate->format('d M Y') . '. Kondisi baik.',
                'is_late' => $isLate,
            ]);

            // Tambahkan lampiran (simulasikan upload file)
            $this->addDummyAttachment($record, 'routine_image_' . $record->id . '.jpg', 'image/jpeg');
            $this->addDummyAttachment($record, 'routine_report_' . $record->id . '.pdf', 'application/pdf');


            // Perbarui jadwal rutin setelah selesai
            $schedule->last_completed_at = $record->completion_date;
            $schedule->next_due_date = Carbon::parse($record->completion_date)->addMonths($schedule->frequency_months);
            $schedule->status = MaintenanceScheduleStatus::SCHEDULED; // Kembali ke terjadwal setelah selesai
            $schedule->save();

            $this->command->info("Rekaman rutin dibuat untuk Unit: Kamar {$record->unit->room_number} (Jadwal {$schedule->id})");
        }

        // === Contoh Rekaman Mendesak (Urgent) ===
        for ($i = 0; $i < 3; $i++) {
            $unit = $units->random(); // Ambil unit yang membutuhkan pemeliharaan secara acak
            $scheduledDate = Carbon::now()->subDays(rand(1, 30));
            $completionDate = $scheduledDate->addHours(rand(1, 24));

            $record = MaintenanceRecord::create([
                'unit_id' => $unit->id,
                'maintenance_schedule_id' => null, // Tidak terkait jadwal rutin
                'type' => MaintenanceRecordType::URGENT,
                'scheduled_date' => $scheduledDate, // Tanggal permintaan
                'completion_date' => $completionDate, // Tanggal selesai aktual
                'status' => MaintenanceRecordStatus::URGENT, // Status selalu URGENT
                'notes' => 'Perbaikan AC mendesak karena ' . ['bocor', 'tidak dingin', 'mati total'][array_rand(['bocor', 'tidak dingin', 'mati total'])],
                'is_late' => false, // Urgent tidak punya konsep terlambat dari jadwal rutin
            ]);

            $this->addDummyAttachment($record, 'urgent_issue_' . ($i+1) . '.jpg', 'image/jpeg');

            $this->command->info("Rekaman mendesak dibuat untuk Unit: Kamar {$record->unit->room_number}");
        }
    }

    /**
     * Helper to add a dummy attachment to a record.
     */
    protected function addDummyAttachment(MaintenanceRecord $record, $fileName = 'dummy_image.jpg', $mimeType = 'image/jpeg')
    {
        $tempDir = storage_path('app/public/temp');
        $dummyFilePath = $tempDir . '/' . $fileName;

        if (Str::startsWith($mimeType, 'image/')) {
            $image = imagecreatetruecolor(100, 100);
            $bgColor = imagecolorallocate($image, rand(0, 255), rand(0, 255), rand(0, 255));
            imagefill($image, 0, 0, $bgColor);
            imagejpeg($image, $dummyFilePath);
            imagedestroy($image);
            $targetPath = 'maintenance_attachments/images';
        } else {
            file_put_contents($dummyFilePath, "This is a dummy {$mimeType} file content for record ID {$record->id}.");
            $targetPath = 'maintenance_attachments/files';
        }

        $path = Storage::disk('public')->putFile($targetPath, new File($dummyFilePath));

        $record->attachments()->create([
            'name' => $fileName,
            'file_name' => basename($path),
            'mime_type' => $mimeType,
            'path' => $path,
        ]);

        unlink($dummyFilePath);
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Report;
use App\Models\Contract;
use App\Models\User;
use App\Enums\ReportStatus;
use App\Enums\ReporterType;
use App\Enums\RoleUser;
use Carbon\Carbon;

class ReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $contracts = Contract::with('occupants', 'pic')->get();
        $staffUsers = User::role(RoleUser::STAFF_OF_RUSUNAWA->value)->get();

        if ($contracts->isEmpty() || $staffUsers->isEmpty()) {
            $this->command->info('Tidak ada data Kontrak atau Staf yang cukup untuk membuat Laporan. Lewati ReportSeeder.');
            return;
        }

        $reportsData = [
            ['subject' => 'Keran Air Bocor di Kamar Mandi', 'description' => 'Keran wastafel di kamar mandi terus menetes meskipun sudah ditutup rapat. Mohon segera diperbaiki untuk menghindari pemborosan air.'],
            ['subject' => 'Lampu Kamar Mati', 'description' => 'Lampu utama di kamar tidur tiba-tiba mati dan tidak bisa dinyalakan kembali. Sudah dicoba ganti bohlam baru tapi tetap tidak berhasil.'],
            ['subject' => 'AC Tidak Dingin', 'description' => 'Pendingin ruangan (AC) di kamar saya tidak mengeluarkan udara dingin, hanya angin biasa. Sepertinya freon habis atau ada masalah lain.'],
            ['subject' => 'Pintu Lemari Rusak', 'description' => 'Engsel salah satu pintu lemari pakaian rusak sehingga pintu tidak bisa ditutup dengan benar.'],
            ['subject' => 'Sambungan Wi-Fi Lemot', 'description' => 'Koneksi internet Wi-Fi di area kamar sangat lambat dan sering terputus dalam beberapa hari terakhir.'],
        ];

        foreach ($reportsData as $data) {
            $contract = $contracts->random();
            $reporter = $contract->occupants->random();
            $handler = $staffUsers->random();

            Report::create([
                'contract_id' => $contract->id,
                'reporter_type' => ReporterType::INDIVIDUAL,
                'reporter_id' => $reporter->id,
                'subject' => $data['subject'],
                'description' => $data['description'],
                'status' => ReportStatus::REPORT_RECEIVED,
                'current_handler_id' => $handler->id,
                'completion_deadline' => null,
            ]);
        }
    }
}
<?php

namespace Database\Seeders;

use App\Models\Unit;
use App\Models\UnitCluster;
use App\Models\UnitType;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semua data yang diperlukan
        $unitTypeIds = UnitType::pluck('id')->toArray();
        $clusters = UnitCluster::all(); // Ambil data semua cluster untuk di-looping

        // Hentikan seeder jika tidak ada cluster untuk menghindari error
        if ($clusters->isEmpty()) {
            $this->command->info('Tidak ada Unit Cluster ditemukan. Seeder Unit dihentikan.');
            return;
        }
        
        // Konfigurasi struktur bangunan
        $totalFloors = 4;  // Setiap gedung memiliki 4 lantai
        $unitsPerFloor = 25; // Setiap lantai memiliki 25 kamar

        $units = []; // Siapkan array kosong untuk menampung semua data unit

        // 1. Looping untuk setiap cluster (misal: Gedung A, lalu Gedung B, dst.)
        foreach ($clusters as $cluster) {
            
            // 2. Di dalam setiap cluster, looping untuk setiap lantai
            for ($floor = 1; $floor <= $totalFloors; $floor++) {
                
                // 3. Di dalam setiap lantai, looping untuk setiap kamar
                for ($unitNumber = 1; $unitNumber <= $unitsPerFloor; $unitNumber++) {

                    // Buat nomor kamar dengan format: [Lantai][NomorUrut] -> 101, 102, ... 425
                    $roomNumber = $floor . Str::padLeft($unitNumber, 2, '0');

                    // Tambahkan data unit baru ke dalam array
                    $units[] = [
                        'room_number' => $roomNumber,
                        'unit_cluster_id' => $cluster->id, // PENTING: Gunakan ID dari cluster saat ini
                        
                        // Data acak lainnya
                        'capacity' => rand(1, 3),
                        'virtual_account_number' => '888' . rand(1000000000000, 9999999999999),
                        'gender_allowed' => fake()->randomElement(['male', 'female', 'general']),
                        'status' => fake()->randomElement(['available', 'not_available', 'occupied', 'under_maintenance']),
                        'unit_type_id' => fake()->randomElement($unitTypeIds),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }
        }

        // Masukkan semua data unit yang sudah di-generate ke database
        collect($units)->chunk(200)->each(function ($chunk) {
            Unit::insert($chunk->toArray());
        });
    }
}

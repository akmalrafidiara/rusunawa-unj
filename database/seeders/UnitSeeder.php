<?php

namespace Database\Seeders;

use App\Models\Unit;
use App\Models\UnitCluster;
use App\Models\UnitType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $unitTypeIds = UnitType::pluck('id')->toArray();
        $unitClusterIds = UnitCluster::pluck('id')->toArray();

        $units = [];

        for ($i = 1; $i <= 50; $i++) {
            $units[] = [
                'room_number' => 'RUSUN-' . Str::padLeft($i, 3, '0'), // RUSUN-001, RUSUN-002, dst.
                'capacity' => rand(1, 4),
                'virtual_account_number' => '888' . rand(1000000000000, 9999999999999),
                'gender_allowed' => fake()->randomElement(['male', 'female', 'general']),
                'status' => fake()->randomElement(['available', 'not_available', 'occupied', 'under_maintenance']),
                'unit_type_id' => fake()->randomElement($unitTypeIds),
                'unit_cluster_id' => fake()->randomElement($unitClusterIds),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Masukkan semua data sekaligus
        Unit::insert($units);
    }
}

<?php

namespace Database\Seeders;

use App\Models\UnitType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $unitTypes = [
            [
                'name' => 'Non AC Non Furnished',
                'description' => 'Unit tanpa AC dan perabotan, cocok untuk penyewa yang ingin membawa perabot sendiri.',
                'facilities' => ['Kamar mandi bersama', 'Dapur', 'Kipas', 'Lemari'],
                'requires_maintenance' => false,
            ],
            [
                'name' => 'Ac Non Furnished',
                'description' => 'Unit dengan AC namun tanpa perabotan, ideal untuk penyewa yang ingin membawa perabot sendiri.',
                'facilities' => ['Kamar mandi dalam', 'Dapur', 'AC'],
                'requires_maintenance' => true,
            ],
            [
                'name' => 'Ac Furnished',
                'description' => 'Unit dengan AC dan perabotan lengkap, siap huni tanpa perlu membawa perabot sendiri.',
                'facilities' => ['Kamar mandi dalam', 'Dapur', 'AC', 'Balkon'],
                'requires_maintenance' => true,
            ],
        ];

        foreach ($unitTypes as $unit) {
            UnitType::firstOrCreate(
                ['name' => $unit['name']], // Unique key untuk pencarian
                [
                    'description' => $unit['description'],
                    'facilities' => $unit['facilities'],
                    'requires_maintenance' => $unit['requires_maintenance'],
                ]
            );
        }
    }
}
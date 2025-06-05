<?php

namespace Database\Seeders;

use App\Models\UnitCluster;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitClusterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $unitClusters = [
            [
                'name' => 'Gedung A',
                'address' => 'Jl. Pemuda No.10, RT.8/RW.5, Rawamangun, Kec. Pulo Gadung, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta 13220',
                'staff_id' => 3,
            ],
            [
                'name' => 'Gedung B',
                'address' => 'Jl. Pemuda No.10, RT.8/RW.5, Rawamangun, Kec. Pulo Gadung, Kota Jakarta Timur, Daerah Khusus Ibukota Jakarta 13220',
                'staff_id' => 4,
            ]
        ];

        foreach ($unitClusters as $unit) {
            UnitCluster::firstOrCreate(
                ['name' => $unit['name']], // Unique key untuk pencarian
                [
                    'address' => $unit['address'],
                    'staff_id' => $unit['staff_id'],
                ]
            );
        }
    }
}

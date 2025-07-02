<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OccupantTypeUnitClusterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $occupantTypeUnitClusters = [
            ['occupant_type_id' => 1, 'unit_cluster_id' => 1],
            ['occupant_type_id' => 1, 'unit_cluster_id' => 2],
            ['occupant_type_id' => 2, 'unit_cluster_id' => 2],
        ];

        DB::table('occupant_type_unit_cluster')->insert($occupantTypeUnitClusters);
    }
}
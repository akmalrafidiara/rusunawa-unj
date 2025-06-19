<?php

namespace Database\Seeders;

use App\Models\UnitType;
use App\Models\UnitRate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class PivotUnitRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $unitTypeIds = UnitType::pluck('id')->toArray();
        $rateIds = UnitRate::pluck('id')->toArray();

        $pivotData = [];

        foreach ($unitTypeIds as $unitId) {
            // Setiap unit punya 1-3 rate yang berbeda
            $randomRates = array_rand($rateIds, rand(1, count($rateIds)));

            // Jika hanya satu rate, ubah jadi array
            if (!is_array($randomRates)) {
                $randomRates = [$randomRates];
            }

            foreach ($randomRates as $key) {
                $rateId = $rateIds[$key];

                $pivotData[] = [
                    'unit_type_id' => $unitId,
                    'rate_id' => $rateId,
                ];
            }
        }

        DB::table('unit_type_rate')->insert($pivotData);

        $this->command->info(count($pivotData) . ' relasi unit_type-rate berhasil disimpan.');
    }
}

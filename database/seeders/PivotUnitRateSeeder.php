<?php

namespace Database\Seeders;

use App\Models\Unit;
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
        $unitIds = Unit::pluck('id')->toArray();
        $rateIds = UnitRate::pluck('id')->toArray();

        $pivotData = [];

        foreach ($unitIds as $unitId) {
            // Setiap unit punya 1-3 rate yang berbeda
            $randomRates = array_rand($rateIds, rand(1, count($rateIds)));

            // Jika hanya satu rate, ubah jadi array
            if (!is_array($randomRates)) {
                $randomRates = [$randomRates];
            }

            foreach ($randomRates as $key) {
                $rateId = $rateIds[$key];

                $pivotData[] = [
                    'unit_id' => $unitId,
                    'rate_id' => $rateId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::table('unit_rate')->insert($pivotData);

        $this->command->info(count($pivotData) . ' relasi unit-rate berhasil disimpan.');
    }
}

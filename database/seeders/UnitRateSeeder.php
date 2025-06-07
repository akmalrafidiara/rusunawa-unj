<?php

namespace Database\Seeders;

use App\Models\UnitRate;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitRateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rates = [
            [
                'price' => 750000,
                'occupant_type' => 'Internal',
                'pricing_bases' => 'per_month',
            ],
            [
                'price' => 150000,
                'occupant_type' => 'Internal',
                'pricing_bases' => 'per_night',
            ],
            [
                'price' => 900000,
                'occupant_type' => 'Eksternal',
                'pricing_bases' => 'per_month',
            ],
            [
                'price' => 200000,
                'occupant_type' => 'Eksternal',
                'pricing_bases' => 'per_night',
            ],
        ];

        foreach ($rates as $rate) {
            UnitRate::firstOrCreate(
                [
                    'price' => $rate['price'],
                    'occupant_type' => $rate['occupant_type'],
                    'pricing_bases' => $rate['pricing_bases'],
                ]
            );
        }
    }
}

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
                'amount' => 750000,
                'tenant_type' => 'Internal',
                'rental_type' => 'Bulanan',
            ],
            [
                'amount' => 150000,
                'tenant_type' => 'Internal',
                'rental_type' => 'Harian',
            ],
            [
                'amount' => 900000,
                'tenant_type' => 'Eksternal',
                'rental_type' => 'Bulanan',
            ],
            [
                'amount' => 200000,
                'tenant_type' => 'Eksternal',
                'rental_type' => 'Harian',
            ],
        ];

        foreach ($rates as $rate) {
            UnitRate::firstOrCreate(
                [
                    'amount' => $rate['amount'],
                    'tenant_type' => $rate['tenant_type'],
                    'rental_type' => $rate['rental_type'],
                ]
            );
        }
    }
}

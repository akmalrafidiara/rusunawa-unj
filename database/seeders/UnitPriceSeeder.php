<?php

namespace Database\Seeders;

use App\Models\OccupantType;
use App\Models\UnitPrice;
use App\Models\UnitType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UnitPriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $unitTypes = UnitType::all();
        $occupantTypes = OccupantType::all();

        // Data harga dari SOP Anda
        $priceMatrix = [
            'Non AC Non Furnished' => [
                'Internal UNJ' => ['per_night' => 135000, 'per_month' => 650000],
                'Eksternal' => ['per_night' => 165000, 'per_month' => 800000],
            ],
            'Ac Non Furnished' => [
                'Internal UNJ' => ['per_night' => 150000, 'per_month' => 1100000],
                'Eksternal' => ['per_night' => 175000, 'per_month' => 1200000],
            ],
            'Ac Furnished' => [
                'Internal UNJ' => ['per_night' => 185000, 'per_month' => 1350000],
                'Eksternal' => ['per_night' => 200000, 'per_month' => 1800000], // Harga range ditangani terpisah
            ],
        ];

        foreach ($unitTypes as $unitType) {
            foreach ($occupantTypes as $occupantType) {
                // Lewati jika tidak ada data harga untuk kombinasi ini
                if (!isset($priceMatrix[$unitType->name][$occupantType->name])) {
                    continue;
                }

                $prices = $priceMatrix[$unitType->name][$occupantType->name];

                // Buat aturan harga untuk harian (per_night)
                UnitPrice::updateOrCreate(
                    [
                        'unit_type_id' => $unitType->id,
                        'occupant_type_id' => $occupantType->id,
                        'pricing_basis' => 'per_night',
                    ],
                    ['price' => $prices['per_night']]
                );

                // Buat aturan harga untuk bulanan (per_month)
                UnitPrice::updateOrCreate(
                    [
                        'unit_type_id' => $unitType->id,
                        'occupant_type_id' => $occupantType->id,
                        'pricing_basis' => 'per_month',
                    ],
                    ['price' => $prices['per_month']]
                );
            }
        }
    }
}

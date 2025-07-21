<?php

namespace Database\Seeders;

use App\Enums\KeyStatus;
use Illuminate\Database\Seeder;
use App\Models\Contract;
use App\Models\Unit;
use App\Models\OccupantType;
use App\Enums\ContractStatus;
use App\Enums\PricingBasis;
use Carbon\Carbon;

class ContractSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $total_contract = (int) $this->command->ask('Berapa banyak kontrak yang ingin dibuat?', 15);

        $units = Unit::where('status', 'available')->get();
        $occupantTypes = OccupantType::all();

        if ($units->isEmpty() || $occupantTypes->isEmpty()) {
            $this->command->info('Tidak ada Unit atau Tipe Penghuni yang tersedia untuk membuat kontrak.');
            return;
        }

        for ($i = 0; $i < $total_contract; $i++) {
            $unit = $units->random();
            $occupantType = $occupantTypes->random();
            $pricingBasis = fake()->randomElement(PricingBasis::values());
            $startDate = Carbon::now()->subDays(rand(0, 30));
            $endDate = ($pricingBasis === 'per_month') ? $startDate->copy()->addMonth() : $startDate->copy()->addDays(rand(1, 10));

            Contract::create([
                'contract_code' => Contract::generateContractCode($unit->unitCluster, $occupantType, $pricingBasis),
                'unit_id' => $unit->id,
                'occupant_type_id' => $occupantType->id,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'pricing_basis' => $pricingBasis,
                'total_price' => fake()->numberBetween(500000, 2000000),
                'status' => fake()->randomElement([ContractStatus::PENDING_PAYMENT, ContractStatus::ACTIVE]),
                'expired_date' => $endDate->copy()->addHours(config('tenancy.initial_payment_due_hours', 2)),
                'key_status' => KeyStatus::PENDING_HANDOVER,
            ]);

            // Tandai unit sebagai tidak tersedia untuk mencegah duplikasi
            $unit->status = 'occupied';
            $unit->save();
        }
    }
}

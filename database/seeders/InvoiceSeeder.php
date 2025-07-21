<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Invoice;
use App\Models\Contract;
use App\Enums\InvoiceStatus;
use Carbon\Carbon;

class InvoiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $total_invoice = (int) ($this->command->ask('Berapa banyak invoice yang ingin dibuat?', 30));
        $contracts = Contract::where('status', 'active')->get();

        if ($contracts->isEmpty()) {
            $this->command->info('Tidak ada kontrak berstatus aktif yang tersedia untuk membuat invoice.');
            return;
        }

        for ($i = 0; $i < $total_invoice; $i++) {
            $contract = $contracts->random();
            $due_at = Carbon::now()->addDays(rand(1, 30));
            $amount = $contract->total_price ?? rand(500000, 2000000);
            $status = fake()->randomElement(InvoiceStatus::values());
            $paid_at = $status === InvoiceStatus::PAID->value ? Carbon::now()->subDays(rand(1, 365)) : null;

            Invoice::create([
                'contract_id' => $contract->id,
                'description' => 'Tagihan untuk kontrak #' . $contract->contract_code,
                'amount' => $amount,
                'due_at' => $due_at,
                'paid_at' => $paid_at,
                'status' => $status,
            ]);
        }
    }
}

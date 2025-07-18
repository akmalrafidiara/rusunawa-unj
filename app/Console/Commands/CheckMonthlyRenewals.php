<?php

namespace App\Console\Commands;

use App\Jobs\GenerateMonthlyRenewalInvoice;
use App\Models\Contract;
use App\Enums\PricingBasis;
use App\Enums\ContractStatus;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class CheckMonthlyRenewals extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'renewals:check-monthly';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks for monthly contracts due for renewal and generates new invoices.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai pengecekan kontrak bulanan untuk perpanjangan...');

        // Ambil kontrak aktif dengan basis harga bulanan
        $monthlyContracts = Contract::where('pricing_basis', PricingBasis::PER_MONTH)
                                    ->where('status', ContractStatus::ACTIVE)
                                    ->get();

        $contractsProcessed = 0;
        foreach ($monthlyContracts as $contract) {
            // Logika pengecekan perpanjangan:
            // Cek jika tanggal akhir kontrak (end_date) kurang dari atau sama dengan 7 hari dari sekarang
            // ATAU jika tanggal akhir kontrak sudah lewat (sudah masuk periode baru)
            $renewalThreshold = Carbon::now()->addDays(7); // Contoh: 7 hari sebelum jatuh tempo
            $alreadyEnded = Carbon::now()->greaterThan($contract->end_date);

            if ($contract->end_date->lessThanOrEqualTo($renewalThreshold) || $alreadyEnded) {
                $this->info("Kontrak #{$contract->contract_code} (Unit: {$contract->unit->room_number}) akan/sudah berakhir pada {$contract->end_date->translatedFormat('d M Y')}. Menyiapkan invoice perpanjangan...");
                GenerateMonthlyRenewalInvoice::dispatch($contract);
                $contractsProcessed++;
            }
        }

        $this->info("Pengecekan kontrak bulanan selesai. {$contractsProcessed} kontrak diproses untuk potensi perpanjangan.");
    }
}

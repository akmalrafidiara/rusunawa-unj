<?php

namespace App\Console\Commands;

use App\Enums\ContractStatus;
use App\Jobs\ProcessInitialContractCancellation;
use App\Models\Invoice;
use Illuminate\Console\Command;

class InitialContractCancellation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contracts:cancel-unpaid-initial';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Membatalkan kontrak awal yang melewati batas waktu pembayaran';

    /**
     * Execute the console command.
     */
    public function handle()
    {
       $this->info('Memeriksa invoice pembayaran pertama yang kedaluwarsa...');

        $expirationHours = 2;

        $expiredFirstInvoices = Invoice::where('status', 'unpaid')
            ->where('created_at', '<=', now()->subHours($expirationHours))
            ->whereHas('contract', function ($query) {
                $query->where('status', ContractStatus::PENDING_PAYMENT->value);
            })
            ->with('contract.unit', 'contract.pic')
            ->get();

        if ($expiredFirstInvoices->isEmpty()) {
            $this->info('Tidak ada pemesanan kedaluwarsa yang ditemukan.');
            return;
        }

        $this->info("Ditemukan {$expiredFirstInvoices->count()} pemesanan yang akan dibatalkan...");

        foreach ($expiredFirstInvoices as $invoice) {
            ProcessInitialContractCancellation::dispatch($invoice);
        }

        $this->info("Berhasil mengirim {$expiredFirstInvoices->count()} tugas pembatalan ke antrean.");
    }
}

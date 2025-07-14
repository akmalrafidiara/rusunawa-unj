<?php

namespace App\Console\Commands;

use App\Enums\ContractStatus;
use App\Enums\UnitStatus;
use App\Mail\ContractCancelledMail;
use App\Models\Contract;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class CancelExpiredContracts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'contracts:cancel-expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Membatalkan kontrak yang melewati batas waktu pembayaran';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memeriksa kontrak yang kedaluwarsa...');

        $expirationHours = 24;

        $expiredContracts = Contract::where('status', ContractStatus::PENDING_PAYMENT)
                                    ->where('created_at', '<=', now()->subHours($expirationHours))
                                    ->with('unit')
                                    ->get();

        if ($expiredContracts->isEmpty()) {
            $this->info('Tidak ada kontrak kedaluwarsa yang ditemukan.');
            return;
        }

        foreach ($expiredContracts as $contract) {
            $contract->update(['status' => ContractStatus::CANCELLED]);

            if ($contract->unit) {
                $contract->unit->update(['status' => UnitStatus::AVAILABLE]);
            }

            $pic = $contract->pic->first();
            if ($pic) {
                Mail::to($pic->email)->send(new ContractCancelledMail($contract));
            }

            Log::info("Kontrak #{$contract->contract_code} otomatis dibatalkan.");
            $this->line("Kontrak #<fg=yellow>{$contract->contract_code}</> telah dibatalkan, unitnya kini tersedia.");

            $this->info('Proses pembatalan kontrak selesai.');
        }
    }
}

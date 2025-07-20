<?php

namespace App\Console\Commands;

use App\Enums\ContractStatus;
use App\Enums\InvoiceStatus;
use App\Enums\OccupantStatus;
use App\Enums\UnitStatus;
use App\Mail\ContractCancelledMail;
use App\Models\Invoice;
use App\Models\Occupant;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ProcessOverdueRenewalCancellation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:cancel-overdue-renewal';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cancels overdue renewal invoices and updates associated contract, unit, and occupant statuses.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai pengecekan invoice perpanjangan yang melewati batas H+7...');

        $cancellationThreshold = Carbon::now()->subDays(7)->startOfDay(); // H+7 dari due_at

        $overdueRenewalInvoices = Invoice::where('status', InvoiceStatus::OVERDUE)
            ->where('due_at', '<=', $cancellationThreshold) // due_at sudah lebih dari 7 hari yang lalu
            ->with('contract.unit', 'contract.occupants', 'contract.pic') // Eager load relationships
            ->get();

        if ($overdueRenewalInvoices->isEmpty()) {
            $this->info('Tidak ada invoice perpanjangan yang memenuhi kriteria pembatalan.');
            return;
        }

        $this->info(sprintf("Ditemukan %d invoice perpanjangan yang akan dibatalkan...", $overdueRenewalInvoices->count()));

        foreach ($overdueRenewalInvoices as $invoice) {
            DB::transaction(function () use ($invoice) {
                $contract = $invoice->contract;

                // 1. Ubah status invoice menjadi CANCELLED
                $invoice->update(['status' => InvoiceStatus::CANCELLED]);

                // 2. Ubah status kontrak menjadi EXPIRED
                $contract->update(['status' => ContractStatus::EXPIRED]);

                // 3. Ubah status unit menjadi AVAILABLE
                if ($contract->unit) {
                    $contract->unit->update(['status' => UnitStatus::AVAILABLE]);
                }

                // 4. Cek masing-masing penghuni di kontrak tersebut
                foreach ($contract->occupants as $occupant) {
                    // Cek apakah penghuni ini masih memiliki kontrak AKTIF lainnya
                    $hasOtherActiveContracts = $occupant->contracts()
                        ->where('id', '!=', $contract->id) // Exclude the current cancelled contract
                        ->where('status', ContractStatus::ACTIVE)
                        ->exists();

                    // Jika tidak memiliki kontrak aktif lainnya, ubah statusnya menjadi INACTIVE
                    if (!$hasOtherActiveContracts && $occupant->status !== OccupantStatus::INACTIVE) {
                        $occupant->update(['status' => OccupantStatus::INACTIVE]);
                        $this->info(sprintf("Penghuni %s diubah menjadi INACTIVE.", $occupant->full_name));
                    }
                }

                // 5. Kirim email notifikasi pembatalan ke PIC kontrak
                $pic = $contract->pic;
                if ($pic && $pic->email) {
                    Mail::to($pic->email)->send(new ContractCancelledMail($contract));
                    $this->info(sprintf("Email pembatalan kontrak #%s dikirim ke PIC %s.", $contract->contract_code, $pic->email));
                } else {
                    Log::warning(sprintf("Tidak dapat mengirim email pembatalan untuk kontrak #%s: PIC atau email tidak ditemukan.", $contract->contract_code));
                }

                Log::info(sprintf("Invoice #%s (perpanjangan) dibatalkan. Kontrak #%s EXPIRED, Unit AVAILABLE.", $invoice->invoice_number, $contract->contract_code));
            });
        }

        $this->info('Pengecekan invoice perpanjangan yang melewati batas H+7 selesai.');
    }
}
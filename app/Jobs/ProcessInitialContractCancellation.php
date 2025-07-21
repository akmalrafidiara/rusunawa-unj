<?php

namespace App\Jobs;

use App\Enums\ContractStatus;
use App\Enums\InvoiceStatus;
use App\Enums\UnitStatus;
use App\Mail\ContractCancelledMail;
use App\Models\Invoice;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ProcessInitialContractCancellation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Invoice $invoice
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::transaction(function () {
            $contract = $this->invoice->contract;

            $this->invoice->update(['status' => InvoiceStatus::CANCELLED]);
            $contract->update(['status' => ContractStatus::CANCELLED]);
            if ($contract->unit) {
                $contract->unit->update(['status' => UnitStatus::AVAILABLE]);
            }

            $pic = $contract->pic;
            if ($pic) {
                Mail::to($pic->email)->send(new ContractCancelledMail($contract));
            }

            Log::info("Kontrak #{$contract->contract_code} otomatis dibatalkan karena invoice pertama tidak dibayar.");
        });
    }
}

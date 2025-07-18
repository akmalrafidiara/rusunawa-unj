<?php

namespace App\Jobs;

use App\Mail\NewInvoiceNotification;
use App\Models\Contract;
use App\Models\Invoice;
use App\Enums\InvoiceStatus;
use App\Enums\PricingBasis;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class GenerateMonthlyRenewalInvoice implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $contract;

    /**
     * Create a new job instance.
     */
    public function __construct(Contract $contract)
    {
        $this->contract = $contract;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->contract->status->value !== \App\Enums\ContractStatus::ACTIVE->value || $this->contract->pricing_basis->value !== PricingBasis::PER_MONTH->value) {
            Log::info("Kontrak {$this->contract->contract_code} tidak memenuhi syarat untuk perpanjangan bulanan otomatis.");
            return;
        }

        // Hitung tanggal mulai dan jatuh tempo untuk invoice berikutnya
        // Invoice berikutnya akan dimulai 1 hari setelah end_date kontrak saat ini
        $nextStartDate = $this->contract->end_date->addDay();
        $nextDueDate = $nextStartDate->copy()->addMonth()->subDay()->endOfDay(); // Satu bulan dari nextStartDate

        // Periksa apakah invoice untuk periode ini sudah ada
        // Kita cari invoice yang tanggal jatuh temponya sama dengan atau setelah nextDueDate
        // Dan belum PAID atau CANCELLED, untuk mencegah duplikasi
        $existingInvoice = Invoice::where('contract_id', $this->contract->id)
            ->where('due_at', '>=', $nextDueDate->startOfDay()) // Jatuh tempo sama atau setelah nextDueDate
            ->whereIn('status', [
                InvoiceStatus::UNPAID,
                InvoiceStatus::PENDING_PAYMENT_VERIFICATION,
                InvoiceStatus::OVERDUE,
            ])
            ->first();

        if ($existingInvoice) {
            Log::info("Invoice untuk perpanjangan kontrak {$this->contract->contract_code} pada periode yang sama sudah ada (Invoice #{$existingInvoice->invoice_number}).");
            return;
        }

        try {
            // Buat invoice baru
            $invoice = new Invoice([
                'contract_id' => $this->contract->id,
                'description' => "Perpanjangan sewa bulanan untuk unit {$this->contract->unit->room_number} ({$this->contract->unit->unitCluster->name}) - Periode " . $nextStartDate->translatedFormat('d M Y') . " s/d " . $nextDueDate->translatedFormat('d M Y'),
                'amount' => $this->contract->total_price, // Gunakan harga total kontrak saat ini sebagai jumlah bulanan
                'due_at' => $nextDueDate,
                'status' => InvoiceStatus::UNPAID,
            ]);
            $invoice->save();

            // Perbarui end_date kontrak jika ini adalah perpanjangan pertama untuk periode baru
            // Ini untuk memastikan kontrak selalu mencerminkan periode terpanjang yang sudah ditagihkan
            $this->contract->update(['end_date' => $nextDueDate]);

            $occupantEmail = $this->contract->occupants->first()->email ?? null;
            if ($occupantEmail) {
                Mail::to($occupantEmail)->send(new NewInvoiceNotification($invoice));
                Log::info("Email notifikasi invoice baru #{$invoice->invoice_number} berhasil dikirim ke {$occupantEmail}.");
            } else {
                Log::warning("Tidak dapat mengirim email notifikasi invoice baru #{$invoice->invoice_number}: Email penghuni tidak ditemukan.");
            }

            Log::info("Berhasil membuat invoice perpanjangan bulanan untuk kontrak {$this->contract->contract_code}: Invoice #{$invoice->invoice_number} (Jumlah: Rp{$invoice->amount}, Jatuh Tempo: {$invoice->due_at->translatedFormat('d M Y')}).");

        } catch (\Exception $e) {
            Log::error("Gagal membuat invoice perpanjangan bulanan untuk kontrak {$this->contract->contract_code}: " . $e->getMessage());
            // Anda bisa menambahkan notifikasi admin atau log lebih lanjut di sini
        }
    }
}

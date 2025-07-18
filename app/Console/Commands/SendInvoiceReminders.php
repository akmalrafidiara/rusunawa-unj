<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Enums\InvoiceStatus;
use App\Mail\InvoiceReminder; // Import Mailable pengingat
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class SendInvoiceReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends reminder emails for upcoming or overdue invoices.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai pengiriman pengingat invoice...');

        $now = Carbon::now();

        // --- Pengingat untuk invoice yang akan jatuh tempo (misal: 3 hari lagi) ---
        $dueSoonInvoices = Invoice::where('status', InvoiceStatus::UNPAID)
            ->where('due_at', '>', $now)
            ->where('due_at', '<=', $now->copy()->addDays(3)->endOfDay()) // Jatuh tempo dalam 3 hari
            ->get();

        foreach ($dueSoonInvoices as $invoice) {
            $occupantEmail = $invoice->contract->occupants->first()->email ?? null;
            if ($occupantEmail) {
                try {
                    Mail::to($occupantEmail)->send(new InvoiceReminder($invoice, 'due_soon'));
                    $this->info("Pengingat 'akan jatuh tempo' untuk Invoice #{$invoice->invoice_number} dikirim ke {$occupantEmail}.");
                } catch (\Exception $e) {
                    Log::error("Gagal mengirim pengingat 'akan jatuh tempo' untuk Invoice #{$invoice->invoice_number}: " . $e->getMessage());
                }
            } else {
                Log::warning("Tidak dapat mengirim pengingat 'akan jatuh tempo' untuk Invoice #{$invoice->invoice_number}: Email penghuni tidak ditemukan.");
            }
        }

        // --- Pengingat untuk invoice yang sudah jatuh tempo (misal: 1 hari setelah jatuh tempo) ---
        $overdueInvoices = Invoice::where('status', InvoiceStatus::UNPAID)
            ->where('due_at', '<', $now->startOfDay()) // Sudah lewat tanggal jatuh tempo
            ->where('due_at', '>=', $now->copy()->subDay()->startOfDay()) // Baru lewat 1 hari
            ->get();

        foreach ($overdueInvoices as $invoice) {
            // Opsional: Ubah status invoice menjadi OVERDUE jika belum
            if ($invoice->status !== InvoiceStatus::OVERDUE) {
                $invoice->update(['status' => InvoiceStatus::OVERDUE]);
            }

            $occupantEmail = $invoice->contract->occupants->first()->email ?? null;
            if ($occupantEmail) {
                try {
                    Mail::to($occupantEmail)->send(new InvoiceReminder($invoice, 'overdue'));
                    $this->info("Pengingat 'sudah jatuh tempo' untuk Invoice #{$invoice->invoice_number} dikirim ke {$occupantEmail}.");
                } catch (\Exception $e) {
                    Log::error("Gagal mengirim pengingat 'sudah jatuh tempo' untuk Invoice #{$invoice->invoice_number}: " . $e->getMessage());
                }
            } else {
                Log::warning("Tidak dapat mengirim pengingat 'sudah jatuh tempo' untuk Invoice #{$invoice->invoice_number}: Email penghuni tidak ditemukan.");
            }
        }

        $this->info('Pengiriman pengingat invoice selesai.');
    }
}

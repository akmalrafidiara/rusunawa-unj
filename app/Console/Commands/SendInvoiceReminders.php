<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Enums\InvoiceStatus;
use App\Mail\InvoiceReminder; // Import Mailable pengingat
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Enums\ContractStatus; // Import ContractStatus

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

        // --- Pengingat untuk invoice yang akan jatuh tempo (1 hari sebelum) ---
        $dueSoonInvoices = Invoice::where('status', InvoiceStatus::UNPAID)
            ->whereDate('due_at', $now->copy()->addDay()->toDateString()) // Exactly 1 day before due date
            ->get();

        foreach ($dueSoonInvoices as $invoice) {
            $occupantEmail = $invoice->contract->pic->email ?? null;
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

        // --- Perbarui status invoice menjadi OVERDUE & kontrak menjadi PENDING_PAYMENT ---
        // Ini dijalankan setelah tanggal jatuh tempo, dan hanya sekali untuk perubahan status
        $invoicesToOverdue = Invoice::where('status', InvoiceStatus::UNPAID)
            ->where('due_at', '<', $now->startOfDay()) // Sudah lewat tanggal jatuh tempo
            ->get();

        foreach ($invoicesToOverdue as $invoice) {
            if ($invoice->status !== InvoiceStatus::OVERDUE->value) {
                $invoice->update(['status' => InvoiceStatus::OVERDUE]);
                $invoice->contract->update(['status' => ContractStatus::PENDING_PAYMENT]);
                $this->info("Invoice #{$invoice->invoice_number} diubah menjadi OVERDUE dan Kontrak #{$invoice->contract->contract_code} menjadi PENDING_PAYMENT.");
            }
        }

        // --- Pengingat harian untuk invoice yang sudah jatuh tempo (hingga H+3) ---
        $overdueRemindersSent = 0;
        $overdueInvoicesForReminders = Invoice::whereIn('status', [InvoiceStatus::OVERDUE, InvoiceStatus::UNPAID])
            ->where('due_at', '<=', $now->startOfDay()) // Past due date
            ->where('due_at', '>', $now->copy()->subDays(3)->startOfDay()) // Not more than 3 days overdue
            ->get();

        foreach ($overdueInvoicesForReminders as $invoice) {
            $occupantEmail = $invoice->contract->pic->email ?? null;
            if ($occupantEmail) {
            try {
                // Send reminder only if it hasn't been sent today for this invoice and type
                // Or if it's the first time it becomes overdue and we need to send the first reminder
                $daysOverdue = $now->diffInDays($invoice->due_at, false);
                if ($daysOverdue >= 0 && $daysOverdue < 3) { // Between 0 (due_at passed) and 2 days overdue
                Mail::to($occupantEmail)->send(new InvoiceReminder($invoice, 'overdue'));
                $this->info("Pengingat 'sudah jatuh tempo' untuk Invoice #{$invoice->invoice_number} (Hari ke {$daysOverdue}) dikirim ke {$occupantEmail}.");
                $overdueRemindersSent++;
                }
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

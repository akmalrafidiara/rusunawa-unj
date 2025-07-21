<?php

namespace App\Mail;

use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceReminder extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $invoice;
    public $reminderType; // 'due_soon' atau 'overdue'

    /**
     * Create a new message instance.
     */
    public function __construct(Invoice $invoice, string $reminderType)
    {
        $this->invoice = $invoice;
        $this->reminderType = $reminderType;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = '';
        if ($this->reminderType === 'due_soon') {
            $subject = 'Pengingat: Tagihan Rusunawa UNJ Anda Akan Jatuh Tempo - #' . $this->invoice->invoice_number;
        } elseif ($this->reminderType === 'overdue') {
            $subject = 'Peringatan: Tagihan Rusunawa UNJ Anda Sudah Jatuh Tempo! - #' . $this->invoice->invoice_number;
        } elseif ($this->reminderType === 'created') {
            $subject = 'Pemberitahuan: Tagihan Rusunawa UNJ Anda Dibuat! - #' . $this->invoice->invoice_number;
        }

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.invoice-reminder',
            with: [
                'invoice' => $this->invoice,
                'occupantName' => $this->invoice->contract->pic->full_name ?? 'Penghuni',
                'unitNumber' => $this->invoice->contract->unit->room_number ?? 'N/A',
                'unitCluster' => $this->invoice->contract->unit->unitCluster->name ?? 'N/A',
                'reminderType' => $this->reminderType,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}

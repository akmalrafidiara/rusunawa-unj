<?php

namespace App\Mail;

use App\Models\Contract;
use App\Models\Invoice;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;

class WelcomeOccupantMail extends Mailable
{
    use Queueable, SerializesModels;

    public Contract $contract;
    public string $url;
    public ?Invoice $invoice;

    /**
     * Create a new message instance.
     *
     * @param Contract $contract
     */
    public function __construct(Contract $contract, string $url, ?Invoice $invoice = null)
    {
        $this->contract = $contract;
        $this->url = $url;
        $this->invoice = $invoice;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Selamat Datang! Akses ke Dashboard Rusunawa Anda',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome-occupant',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        if ($this->invoice) {
            return [
                Attachment::fromData(fn () => $this->invoice->generatePdf(), 'Invoice-' . $this->invoice->invoice_number . '.pdf')
                    ->withMime('application/pdf'),
            ];
        }
        
        return [];
    }
}

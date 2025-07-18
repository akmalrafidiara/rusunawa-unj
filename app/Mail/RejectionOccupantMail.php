<?php

namespace App\Mail;

use App\Models\Occupant;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RejectionOccupantMail extends Mailable
{
    use Queueable, SerializesModels;

    public Occupant $occupant;
    public string $responseMessage;

    /**
     * Create a new message instance.
     *
     * @param Occupant $occupant
     */
    public function __construct(Occupant $occupant, string $responseMessage)
    {
        $this->occupant = $occupant;
        $this->responseMessage = $responseMessage;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Data Verifikasi Penghuni Rusunawa Ditolak',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.rejection-occupant',
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

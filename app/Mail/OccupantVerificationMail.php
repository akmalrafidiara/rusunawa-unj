<?php

namespace App\Mail;

use App\Enums\VerificationStatus;
use App\Models\Contract;
use App\Models\Occupant;
use App\Models\VerificationLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OccupantVerificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $occupant;
    public $contract;
    public $verificationLog;

    /**
     * Create a new message instance.
     */
    public function __construct(Occupant $occupant, Contract $contract, VerificationLog $verificationLog)
    {
        $this->occupant = $occupant;
        $this->contract = $contract;
        $this->verificationLog = $verificationLog;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = ($this->verificationLog->status === VerificationStatus::APPROVED)
                   ? 'Verifikasi Penghuni Anda Telah Disetujui!'
                   : 'Verifikasi Penghuni Anda Ditolak';

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $view = ($this->verificationLog->status === VerificationStatus::APPROVED)
                ? 'emails.occupant-verification.approved'
                : 'emails.occupant-verification.rejected';

        return new Content(
            view: $view,
            with: [
                'occupant' => $this->occupant,
                'contract' => $this->contract,
                'verificationLog' => $this->verificationLog,
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
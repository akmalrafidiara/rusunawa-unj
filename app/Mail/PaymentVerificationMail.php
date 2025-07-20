<?php

namespace App\Mail;

use App\Enums\VerificationStatus;
use App\Models\Payment;
use App\Models\VerificationLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PaymentVerificationMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $payment;
    public $verificationLog;

    /**
     * Create a new message instance.
     */
    public function __construct(Payment $payment, VerificationLog $verificationLog)
    {
        $this->payment = $payment;
        $this->verificationLog = $verificationLog;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = ($this->verificationLog->status === VerificationStatus::APPROVED)
                   ? 'Verifikasi Pembayaran Anda Telah Disetujui!'
                   : 'Verifikasi Pembayaran Anda Ditolak';

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
                ? 'emails.payment-verification.approved'
                : 'emails.payment-verification.rejected';

        return new Content(
            view: $view,
            with: [
                'payment' => $this->payment,
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

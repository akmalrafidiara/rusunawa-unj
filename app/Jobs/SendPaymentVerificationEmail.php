<?php

namespace App\Jobs;

use App\Mail\PaymentVerificationMail;
use App\Models\Payment;
use App\Models\VerificationLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendPaymentVerificationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $payment;
    public $verificationLog;

    /**
     * Create a new job instance.
     */
    public function __construct(Payment $payment, VerificationLog $verificationLog)
    {
        $this->payment = $payment;
        $this->verificationLog = $verificationLog;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->payment->invoice->contract->pic->email)->send(new PaymentVerificationMail($this->payment, $this->verificationLog));
    }
}

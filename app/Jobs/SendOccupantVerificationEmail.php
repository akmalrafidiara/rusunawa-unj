<?php

namespace App\Jobs;

use App\Mail\OccupantVerificationMail;
use App\Models\Contract;
use App\Models\Occupant;
use App\Models\VerificationLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendOccupantVerificationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public Occupant $occupant;
    public Contract $contract;
    public VerificationLog $verificationLog;

    /**
     * Create a new job instance.
     */
    public function __construct(Occupant $occupant, Contract $contract, VerificationLog $verificationLog)
    {
        $this->occupant = $occupant;
        $this->contract = $contract;
        $this->verificationLog = $verificationLog;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->occupant->email)->send(new OccupantVerificationMail($this->occupant, $this->contract, $this->verificationLog));
    }
}

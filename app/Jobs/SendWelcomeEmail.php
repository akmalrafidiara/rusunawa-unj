<?php

namespace App\Jobs;

use App\Mail\WelcomeOccupantMail;
use App\Models\Contract;
use App\Models\Occupant;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendWelcomeEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $occupant;
    protected $contract;
    protected $authUrl;
    protected $invoice;

    public function __construct(Occupant $occupant, Contract $contract, $authUrl = null, $invoice = null)
    {
        $this->occupant = $occupant;
        $this->contract = $contract;
        $this->authUrl = $authUrl;
        $this->invoice = $invoice;
    }

    public function handle(): void
    {
        Mail::to($this->occupant->email)->send(new WelcomeOccupantMail($this->contract, $this->authUrl, $this->invoice));
    }
}

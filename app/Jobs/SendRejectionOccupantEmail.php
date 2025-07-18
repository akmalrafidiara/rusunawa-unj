<?php

namespace App\Jobs;

use App\Mail\RejectionOccupantMail;
use App\Mail\WelcomeOccupantMail;
use App\Models\Contract;
use App\Models\Occupant;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendRejectionOccupantEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $occupant;
    protected $responseMessage;

    public function __construct(Occupant $occupant, $responseMessage)
    {
        $this->occupant = $occupant;
        $this->responseMessage = $responseMessage;
    }

    public function handle(): void
    {
        Mail::to($this->occupant->email)->send(new RejectionOccupantMail($this->occupant, $this->responseMessage));
    }
}

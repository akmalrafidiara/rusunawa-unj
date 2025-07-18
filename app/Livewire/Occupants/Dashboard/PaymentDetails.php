<?php

namespace App\Livewire\Occupants\Dashboard;

use App\Models\Contract;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class PaymentDetails extends Component
{
    public ?Contract $contract;
    public ?Invoice $latestInvoice;

    public function mount()
    {
        $occupant = Auth::guard('occupant')->user();
        $this->contract = $occupant?->contracts()->with('unit')->first();
        if ($this->contract) {
            $this->latestInvoice = $this->contract->invoices()->latest()->first();
        }
    }

    public function render()
    {
        return view('livewire.occupants.dashboard.payment-details');
    }
}
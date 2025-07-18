<?php

namespace App\Livewire\Occupants\Dashboard;

use App\Models\Contract;
use App\Models\Unit;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UnitDetails extends Component
{
    public ?Unit $unit;
    public ?Contract $contract;

    public function mount()
    {
        $occupant = Auth::guard('occupant')->user();

        /** 
         * @var \App\Models\Occupant $occupant
         */
        $this->contract = $occupant?->contracts()->with('unit')->first();
        $this->unit = $this->contract?->unit;
    }

    public function render()
    {
        return view('livewire.occupants.dashboard.unit-details');
    }
}

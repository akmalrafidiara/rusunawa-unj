<?php

namespace App\Livewire\Occupants\Dashboard;

use App\Models\Contract;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class OccupantData extends Component
{
    public ?Contract $contract;

    public function mount()
    {
        $occupant = Auth::guard('occupant')->user();
        $this->contract = $occupant?->contracts()->with('occupants')->first();
    }

    public function render()
    {
        return view('livewire.occupants.dashboard.occupant-data');
    }
}
<?php

namespace App\Livewire\Occupants\Dashboard;

use App\Models\Contract;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Complaints extends Component
{
    public $complaints;

    public function mount()
    {
        $occupant = Auth::guard('occupant')->user();
        $contract = $occupant?->contracts()->first();
        if ($contract) {
            $this->complaints = $contract->reports()->latest()->take(3)->get();
        } else {
            $this->complaints = collect();
        }
    }

    public function render()
    {
        return view('livewire.occupants.dashboard.complaints');
    }
}
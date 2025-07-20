<?php

namespace App\Livewire\Contracts\Dashboard;

use App\Models\Contract;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Complaints extends Component
{
    public $complaints;

    public function mount()
    {
        /**
         * @var Contract $contract
         */
        $contract = Auth::guard('contract')->user();
        if ($contract) {
            $this->complaints = $contract->reports()->latest()->take(3)->get();
        } else {
            $this->complaints = collect();
        }
    }

    public function render()
    {
        return view('livewire.contracts.dashboard.complaints');
    }
}

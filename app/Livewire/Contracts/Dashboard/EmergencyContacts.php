<?php

namespace App\Livewire\Contracts\Dashboard;

use App\Models\Contact;
use Livewire\Component;

class EmergencyContacts extends Component
{
    public $contacts;

    public function mount()
    {
        $this->contacts = Contact::all();
    }

    public function render()
    {
        return view('livewire.contracts.dashboard.emergency-contacts');
    }
}

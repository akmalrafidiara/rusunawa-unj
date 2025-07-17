<?php

namespace App\Livewire\Occupants\Dashboard;

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
        return view('livewire.occupants.dashboard.emergency-contacts');
    }
}
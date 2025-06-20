<?php

namespace App\Livewire\Managers;

use App\Models\Content;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

class Contact extends Component
{
    public $phoneNumber;
    public $operationalHours;
    public $address;
    public $email;

    public function mount()
    {
        $this->phoneNumber = Content::where('content_key', 'contact_phone_number')->first()->content_value ?? '';
        $this->operationalHours = Content::where('content_key', 'contact_operational_hours')->first()->content_value ?? '';
        $this->address = Content::where('content_key', 'contact_address')->first()->content_value ?? '';
        $this->email = Content::where('content_key', 'contact_email')->first()->content_value ?? '';
    }

    public function save()
    {
        $this->validate([
            'phoneNumber' => 'required|string|max:50',
            'operationalHours' => 'required|string|max:100',
            'address' => 'required|string|max:200',
            'email' => 'required|email|max:255',
        ]);

        Content::updateOrCreate(
            ['content_key' => 'contact_phone_number'],
            ['content_value' => $this->phoneNumber, 'content_type' => 'text']
        );

        Content::updateOrCreate(
            ['content_key' => 'contact_operational_hours'],
            ['content_value' => $this->operationalHours, 'content_type' => 'text']
        );

        Content::updateOrCreate(
            ['content_key' => 'contact_address'],
            ['content_value' => $this->address, 'content_type' => 'text']
        );

        Content::updateOrCreate(
            ['content_key' => 'contact_email'],
            ['content_value' => $this->email, 'content_type' => 'email']
        );

        LivewireAlert::title('Konten Kontak berhasil diperbarui!')
            ->success()
            ->toast()
            ->position('top-end')
            ->show();
    }

    public function render()
    {
        return view('livewire.managers.contents.contacts.index');
    }
}
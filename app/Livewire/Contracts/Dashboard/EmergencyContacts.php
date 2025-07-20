<?php

namespace App\Livewire\Contracts\Dashboard;

use Livewire\Component;
use App\Models\Contact;
use App\Enums\EmergencyContactRole;

class EmergencyContacts extends Component
{
    public $allContacts; // Semua kontak yang diambil dari database
    public $displayedContacts; // Kontak yang saat ini ditampilkan
    public $initialLimit = 6; // Batas awal kontak yang ditampilkan
    public $limit; // Batas saat ini

    public function mount(): void
    {
        // Atur batas saat ini ke batas awal saat komponen pertama kali dimuat
        $this->limit = $this->initialLimit;
        
        // Ambil kontak internal
        $internalContacts = Contact::where('role', EmergencyContactRole::Internal->value)->get();

        // Ambil kontak eksternal
        $externalContacts = Contact::where('role', EmergencyContactRole::External->value)->get();

        // Gabungkan keduanya, pastikan kontak internal muncul lebih dulu
        $this->allContacts = $internalContacts->concat($externalContacts);

        // Atur kontak yang akan ditampilkan pada awalnya
        $this->updateDisplayedContacts();
    }

    public function loadMore(): void
    {
        // Tambahkan batas sebanyak 6
        $this->limit += 6; 
        $this->updateDisplayedContacts(); // Perbarui kontak yang ditampilkan
    }

    public function showLess(): void
    {
        // Kembalikan batas ke nilai awal
        $this->limit = $this->initialLimit;
        $this->updateDisplayedContacts();
    }

    private function updateDisplayedContacts(): void
    {
        $this->displayedContacts = $this->allContacts->take($this->limit);
    }

    public function render()
    {
        return view('livewire.contracts.dashboard.emergency-contacts');
    }
}
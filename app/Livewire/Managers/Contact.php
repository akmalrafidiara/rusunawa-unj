<?php

namespace App\Livewire\Managers;

use App\Models\Content;
use Livewire\Component;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use Illuminate\Validation\ValidationException; // Import ValidationException

class Contact extends Component
{
    public $phoneNumber;
    public $operationalHours;
    public $address;
    public $email;

    protected $rules = [
        'phoneNumber' => 'required|numeric|max_digits:20',
        'operationalHours' => 'required|string|max:100',
        'address' => 'required|string|max:200',
        'email' => 'required|email|max:255',
    ];

    // PESAN VALIDASI DALAM BAHASA INDONESIA
    protected $messages = [
        'phoneNumber.required' => 'Kolom Nomor Telepon wajib diisi.',
        'phoneNumber.numeric' => 'Nomor Telepon harus berupa angka.',
        'phoneNumber.max_digits' => 'Nomor Telepon tidak boleh lebih dari :max digit.',

        'operationalHours.required' => 'Kolom Jam Operasional wajib diisi.',
        'operationalHours.string' => 'Jam Operasional harus berupa teks.',
        'operationalHours.max' => 'Jam Operasional tidak boleh lebih dari :max karakter.',

        'address.required' => 'Kolom Alamat wajib diisi.',
        'address.string' => 'Alamat harus berupa teks.',
        'address.max' => 'Alamat tidak boleh lebih dari :max karakter.',

        'email.required' => 'Kolom Email wajib diisi.',
        'email.email' => 'Format Email tidak valid. Harap masukkan alamat email yang benar.',
        'email.max' => 'Email tidak boleh lebih dari :max karakter.',
    ];

    public function mount()
    {
        $this->phoneNumber = optional(Content::where('content_key', 'contact_phone_number')->first())->content_value ?? '';
        $this->operationalHours = optional(Content::where('content_key', 'contact_operational_hours')->first())->content_value ?? '';
        $this->address = optional(Content::where('content_key', 'contact_address')->first())->content_value ?? '';
        $this->email = optional(Content::where('content_key', 'contact_email')->first())->content_value ?? '';
    }

    public function render()
    {
        return view('livewire.managers.contents.contacts.index');
    }

    public function save()
    {
        try {
            $this->validate(); // Coba lakukan validasi
        } catch (ValidationException $e) { // Tangkap pengecualian validasi
            LivewireAlert::error()
                ->title('Mohon lengkapi semua data yang wajib diisi!') // Pop-up umum
                ->text('Harap periksa kembali kolom yang bertanda merah.') // Teks tambahan
                ->toast()
                ->position('top-end')
                ->show();
            throw $e; // Lempar kembali pengecualian agar pesan merah di bawah form tetap muncul
        }

        // Jika validasi berhasil, lanjutkan proses penyimpanan
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
}
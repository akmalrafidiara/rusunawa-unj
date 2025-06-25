<?php

use function Livewire\Volt\{state};
use App\Models\GuestQuestion; // Impor model GuestQuestion
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert; // Impor LivewireAlert jika digunakan
use Illuminate\Validation\ValidationException; // Impor ValidationException

// Mendefinisikan properti state untuk form
state([
    'fullName' => '',
    'formPhoneNumber' => '',
    'formEmail' => '',
    'message' => '',
]);

// Aturan validasi untuk form
$rules = function () {
    return [
        'fullName' => 'required|string|max:255',
        'formPhoneNumber' => 'nullable|string|max:20',
        'formEmail' => 'required|email|max:255',
        'message' => 'required|string|max:200', // Sesuai gambar ada counter 0/200
    ];
};

// Metode untuk memproses submit form
$submitForm = function () {
    try {
        $this->validate($this->rules()); // Validasi input form

        // Pastikan Anda mengirimkan semua data yang diperlukan sesuai nama kolom di database
        GuestQuestion::create([
            'fullName' => $this->fullName,         // Kolom 'full_name'
            'formPhoneNumber' => $this->formPhoneNumber, // Kolom 'phone_number'
            'formEmail' => $this->formEmail,             // Kolom 'email'
            'message' => $this->message,             // Kolom 'message'
        ]);

        LivewireAlert::title('Pesan Terkirim!')
            ->text('Terima kasih, pesan Anda telah berhasil dikirim. Kami akan menghubungi Anda kembali secepatnya.')
            ->success()
            ->toast()
            ->position('top-end')
            ->show();

        // Reset form setelah sukses
        $this->reset(['fullName', 'formPhoneNumber', 'formEmail', 'message']);

    } catch (ValidationException $e) {
        LivewireAlert::error()
            ->title('Gagal Kirim Pesan')
            ->text('Mohon periksa kembali input Anda.')
            ->toast()
            ->position('top-end')
            ->show();
        throw $e; // Lempar kembali exception agar Livewire menangani error bag
    } catch (\Exception $e) {
        LivewireAlert::title('Gagal Kirim Pesan')
            ->text('Terjadi kesalahan saat mengirim pesan: ' . $e->getMessage())
            ->error()
            ->toast()
            ->position('top-end')
            ->show();
    }
};

?>

{{-- Kolom Kanan: Form Pertanyaan --}}
{{-- x-data="{ messageLength: $wire.message.length }" digunakan untuk menghitung karakter pesan dengan Alpine.js --}}
<div x-data="{ messageLength: $wire.message.length }" class="bg-gray-50 p-6 rounded-lg shadow-inner border border-gray-200">
    <h4 class="text-xl font-bold text-gray-800 mb-4">Kirim Pesan Kepada Kami</h4>
    <form wire:submit.prevent="submitForm" class="space-y-4">
        <div>
            <label for="fullName" class="block text-gray-700 text-sm font-semibold mb-2">Nama Lengkap <span class="text-red-500">*</span></label>
            <input type="text" id="fullName" wire:model.live="fullName" placeholder="Nama lengkap Anda"
                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 @error('fullName') border-red-500 @enderror">
            @error('fullName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="formPhoneNumber" class="block text-gray-700 text-sm font-semibold mb-2">Nomor Telepon</label>
            <input type="text" id="formPhoneNumber" wire:model.live="formPhoneNumber" placeholder="Nomor telepon Anda"
                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 @error('formPhoneNumber') border-red-500 @enderror">
            @error('formPhoneNumber') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="formEmail" class="block text-gray-700 text-sm font-semibold mb-2">Email <span class="text-red-500">*</span></label>
            <input type="email" id="formEmail" wire:model.live="formEmail" placeholder="Email yang bisa dihubungi"
                   class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 @error('formEmail') border-red-500 @enderror">
            @error('formEmail') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
        <div>
            <label for="message" class="block text-gray-700 text-sm font-semibold mb-2">Pesan <span class="text-red-500">*</span></label>
            <textarea id="message" wire:model.live="message" x-on:input="messageLength = $wire.message.length" rows="5" placeholder="Isi pesan" maxlength="200"
                      class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500 @error('message') border-red-500 @enderror"></textarea>
            <div class="text-right text-xs text-gray-500">
                <span x-text="messageLength"></span>/200
            </div>
            @error('message') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
        </div>
        <button type="submit"
                class="w-full bg-green-600 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-md transition-colors duration-300 shadow-md">
            Submit
        </button>
    </form>
</div>

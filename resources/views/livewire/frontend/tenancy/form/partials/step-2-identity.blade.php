@php
    $inputBaseClass =
        'w-full mt-1 block py-2 px-3 border bg-white dark:bg-zinc-700 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 transition';
@endphp

<div>
    @php
        // Variabel ini akan kita gunakan untuk semua input agar stylenya sama
        $inputBaseClass =
            'w-full mt-1 block py-2 px-3 border dark:bg-zinc-700 rounded-md shadow-sm focus:outline-none focus:ring-1 focus:ring-emerald-500 focus:border-emerald-500 transition';
    @endphp

    <div class="space-y-6">
        {{-- Baris 1: Nama Lengkap (Lebar Penuh) --}}
        <div>
            <label for="fullName" class="block text-sm font-medium mb-1">
                Nama Penghuni <span class="text-red-500">*</span>
            </label>
            <input id="fullName" type="text" wire:model="fullName" placeholder="Nama Lengkap"
                class="{{ $inputBaseClass }} {{ $errors->has('fullName') ? 'border-red-500' : 'border-gray-600' }}">
            @error('fullName')
                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
            @enderror
        </div>

        {{-- Baris 2: Email & Nomor Whatsapp (2 Kolom) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="email" class="block text-sm font-medium mb-1">
                    Email <span class="text-red-500">*</span>
                </label>
                <input id="email" type="email" wire:model="email" placeholder="Email yang bisa dihubungi"
                    class="{{ $inputBaseClass }} {{ $errors->has('email') ? 'border-red-500' : 'border-gray-600' }}">
                @error('email')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                @enderror
            </div>
            <div>
                <label for="whatsappNumber" class="block text-sm font-medium mb-1">
                    Nomor Whatsapp <span class="text-red-500">*</span>
                </label>
                <input id="whatsappNumber" type="tel" wire:model="whatsappNumber" placeholder="08xxxxxxxxxx"
                    class="{{ $inputBaseClass }} {{ $errors->has('whatsappNumber') ? 'border-red-500' : 'border-gray-600' }}">
                @error('whatsappNumber')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                @enderror
            </div>
        </div>

        {{-- Baris 3: Upload File (2 Kolom) --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            {{-- Upload Kartu Identitas --}}
            <div>
                <label for="identityCardFile" class="block text-sm font-medium mb-1">
                    Kartu Identitas <span class="text-red-500">*</span>
                </label>
                <div
                    class="mt-1 flex items-center justify-between w-full border rounded-lg px-3 py-2 {{ $errors->has('identityCardFile') ? 'border-red-500' : 'border-gray-600' }}">
                    <span class="text-gray-400 text-sm truncate">
                        Format: .jpg, .pdf (Maks 10MB)
                    </span>
                    <label for="identityCardFile"
                        class="bg-emerald-600 text-white text-sm font-semibold px-4 py-1 rounded-md cursor-pointer hover:bg-emerald-700 transition">
                        Upload
                    </label>
                    <input id="identityCardFile" type="file" wire:model="identityCardFile" class="hidden">
                </div>
                <p class="text-xs text-gray-400 mt-2">* Diisi dengan KTM khusus Mahasiswa dan KTP selain Mahasiswa</p>
                @error('identityCardFile')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                @enderror
            </div>

            {{-- Upload Bukti Keanggotaan --}}
            <div>
                <label for="communityCardFile" class="block text-sm font-medium mb-1">
                    Bukti Keanggotaan
                </label>
                <div
                    class="mt-1 flex items-center justify-between w-full border rounded-lg px-3 py-2 {{ $errors->has('communityCardFile') ? 'border-red-500' : 'border-gray-600' }}">
                    <span class="text-gray-400 text-sm truncate">
                        Format: .jpg, .pdf (Maks 10MB)
                    </span>
                    <label for="communityCardFile"
                        class="bg-emerald-600 text-white text-sm font-semibold px-4 py-1 rounded-md cursor-pointer hover:bg-emerald-700 transition">
                        Upload
                    </label>
                    <input id="communityCardFile" type="file" wire:model="communityCardFile" class="hidden">
                </div>
                <ul class="text-xs text-gray-400 mt-2 list-disc list-inside space-y-1">
                    <li>Diisi dengan Surat Tugas, atau Surat Kerjasama Resmi dengan UNJ</li>
                    <li><span class="font-semibold">Penghuni umum tanpa surat</span> tidak perlu mengisi bagian ini</li>
                </ul>
                @error('communityCardFile')
                    <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    {{-- Tombol Aksi --}}
    <div class="mt-8 flex gap-2">
        <button wire:click="previousStep"
            class="border border-emerald-600 text-emerald-600 font-semibold px-6 py-2 rounded-lg cursor-pointer">Kembali</button>
        <button wire:click="firstStepSubmit"
            class="bg-emerald-600 text-white font-semibold px-6 py-2 rounded-lg cursor-pointer">Selanjutnya</button>
    </div>
</div>

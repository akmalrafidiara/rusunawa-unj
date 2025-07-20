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
        {{-- Baris 1 & 2: Data Diri --}}
        <x-frontend.form.input name="fullName" label="Nama Penghuni" placeholder="Nama Lengkap" />

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-frontend.form.input name="email" type="email" label="Email" placeholder="Email yang bisa dihubungi" />
            <x-frontend.form.input name="whatsappNumber" whatsapp type="number" label="Nomor WhatsApp"
                placeholder="8xxxxxxxxxx" />
        </div>

        {{-- Baris 3: Upload File --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <x-frontend.form.file name="identityCardFile" label="Kartu Identitas"
                helpText="* Diisi dengan KTM khusus Mahasiswa dan KTP selain Mahasiswa"
                accept="image/jpeg,image/png,image/jpg,application/pdf" />
            @if ($occupantType->requires_verification)
                <x-frontend.form.file name="communityCardFile" label="Bukti Keanggotaan" :required="false"
                    accept="image/jpeg,image/png,image/jpg,application/pdf">
                    <x-slot:helpText>
                        <ul class="text-xs text-gray-400 mt-2 list-disc list-inside space-y-1">
                            <li>Diisi dengan Surat Tugas, atau Surat Kerjasama Resmi dengan UNJ</li>
                            <li>
                                <span class="font-semibold">Penghuni umum tanpa surat</span> tidak perlu mengisi bagian
                                ini
                                <a href="{{ $filterUrl }}" class="text-emerald-600 underline">Ganti Filter</a>
                            </li>
                        </ul>
                    </x-slot:helpText>
                </x-frontend.form.file>
            @endif
        </div>

        {{-- Baris 4: Verifikasi Mahasiswa --}}
        <div>
            <label class="block text-sm font-medium mb-1">
                Apakah Anda Mahasiswa UNJ/Pertukaran?
            </label>
            <div class="flex items-center space-x-2">
                <input id="isStudent" type="checkbox" wire:model.live="isStudent" value="1"
                    class="w-4 h-4 text-emerald-600 bg-white border-gray-300 rounded focus:ring-emerald-500 focus:ring-2">
                <label for="isStudent" class="text-sm font-medium cursor-pointer">
                    Ya, saya mahasiswa UNJ/Pertukaran
                </label>
            </div>
            @error('isStudent')
                <span class="text-red-500 text-xs mt-1">{{ $message }}</span>
            @enderror
        </div>
        {{-- Form Data Akademik (jika mahasiswa) --}}
        @if ($studentForm)
            <div class="mt-6 space-y-6">
                <h4 class="text-md font-semibold">Lengkapi Data Akademik</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <x-frontend.form.input name="studentId" label="NIM/NRM" type="number"
                        placeholder="Contoh: 13xxxxxxxx" />
                    <x-frontend.form.select name="faculty" label="Fakultas" :options="$facultyOptions" />
                    <x-frontend.form.select name="studyProgram" label="Program Studi" :options="$studyProgramOptions"
                        :disabled="empty($studyProgramOptions)" />
                    <x-frontend.form.select name="classYear" label="Angkatan" :options="$classYearOptions" />
                </div>
            </div>
        @endif
    </div>


    {{-- Tombol Aksi --}}
    <div class="mt-8 flex gap-2">
        <button wire:click="previousStep"
            class="border border-emerald-600 text-emerald-600 font-semibold px-6 py-2 rounded-lg cursor-pointer">Kembali</button>
        <button wire:click="secondStepSubmit"
            class="bg-emerald-600 text-white font-semibold px-6 py-2 rounded-lg cursor-pointer">Selanjutnya</button>
    </div>
</div>

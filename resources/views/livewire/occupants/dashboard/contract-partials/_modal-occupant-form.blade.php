<x-managers.ui.modal title="Edit Data Penghuni" :show="$showModal && $modalType === 'occupant'" class="max-w-2xl">
    <div x-data="{ isStudent: @entangle('isStudent') }">
        <form wire:submit.prevent="saveOccupant" class="space-y-4">
            {{-- Bagian Data Diri Utama --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Nama Lengkap --}}
                <div>
                    <x-managers.form.label>Nama Lengkap</x-managers.form.label>
                    <x-managers.form.input wire:model="fullName" placeholder="Masukkan nama lengkap" required />
                </div>
                {{-- Email --}}
                <div>
                    <x-managers.form.label>Alamat Email</x-managers.form.label>
                    <x-managers.form.input wire:model="email" type="email" placeholder="contoh@email.com" required />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Nomor WhatsApp --}}
                <div>
                    <x-managers.form.label>Nomor WhatsApp</x-managers.form.label>
                    <x-managers.form.input wire:model="whatsappNumber" placeholder="WhatsApp Number" type="text" />
                </div>
                {{-- Jenis Kelamin --}}
                <div>
                    <x-managers.form.label>Jenis Kelamin</x-managers.form.label>
                    <x-managers.form.select wire:model="gender" :options="$genderOptions" label="Pilih Jenis Kelamin" />
                </div>
            </div>

            <hr class="my-6">

            {{-- Bagian Upload Dokumen --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- File KTP --}}
                <div>
                    <x-managers.form.label>File KTP</x-managers.form.label>
                    @if ($identityCardFile)
                        <div class="inline-flex gap-2 border border-gray-300 rounded p-2 mb-2">
                            <x-managers.form.small>Preview</x-managers.form.small>
                            @php
                                $fileUrl =
                                    $identityCardFile instanceof \Illuminate\Http\UploadedFile
                                        ? $identityCardFile->temporaryUrl()
                                        : asset('storage/' . $identityCardFile);
                                $fileName =
                                    $identityCardFile instanceof \Illuminate\Http\UploadedFile
                                        ? $identityCardFile->getClientOriginalName()
                                        : basename($identityCardFile);
                                $isImage = in_array(strtolower(pathinfo($fileName, PATHINFO_EXTENSION)), [
                                    'jpg',
                                    'jpeg',
                                    'png',
                                    'gif',
                                ]);
                            @endphp
                            @if ($isImage)
                                <img src="{{ $fileUrl }}" alt="Preview Gambar"
                                    class="w-16 h-16 object-cover rounded border" />
                            @else
                                <div class="w-16 h-16 flex items-center justify-center bg-gray-100 rounded border">
                                    <span class="text-xs text-gray-600">PDF</span>
                                </div>
                            @endif
                        </div>
                    @endif

                    <div class="mb-2">
                        @if ($errors->has('identityCardFile'))
                            <span class="text-red-500 text-sm">{{ $errors->first('identityCardFile') }}</span>
                        @else
                            <x-managers.form.small>Max 2MB. JPG, PNG, PDF</x-managers.form.small>
                        @endif
                    </div>

                    <x-filepond::upload wire:model.live="identityCardFile" />
                </div>
                {{-- File Kartu Komunitas/Keluarga --}}
                <div>
                    <x-managers.form.label>Kartu Komunitas/KK (Opsional)</x-managers.form.label>
                    @if ($communityCardFile)
                        <div class="inline-flex gap-2 border border-gray-300 rounded p-2 mb-2">
                            <x-managers.form.small>Preview</x-managers.form.small>
                            @php
                                $fileUrl =
                                    $communityCardFile instanceof \Illuminate\Http\UploadedFile
                                        ? $communityCardFile->temporaryUrl()
                                        : asset('storage/' . $communityCardFile);
                                $fileName =
                                    $communityCardFile instanceof \Illuminate\Http\UploadedFile
                                        ? $communityCardFile->getClientOriginalName()
                                        : basename($communityCardFile);
                                $isImage = in_array(strtolower(pathinfo($fileName, PATHINFO_EXTENSION)), [
                                    'jpg',
                                    'jpeg',
                                    'png',
                                    'gif',
                                ]);
                            @endphp
                            @if ($isImage)
                                <img src="{{ $fileUrl }}" alt="Preview Gambar"
                                    class="w-16 h-16 object-cover rounded border" />
                            @else
                                <div class="w-16 h-16 flex items-center justify-center bg-gray-100 rounded border">
                                    <span class="text-xs text-gray-600">PDF</span>
                                </div>
                            @endif
                        </div>
                    @endif

                    <div class="mb-2">
                        @if ($errors->has('communityCardFile'))
                            <span class="text-red-500 text-sm">{{ $errors->first('communityCardFile') }}</span>
                        @else
                            <x-managers.form.small>Max 2MB. JPG, PNG, PDF</x-managers.form.small>
                        @endif
                    </div>

                    <x-filepond::upload wire:model.live="communityCardFile" />
                </div>
            </div>

            <hr class="my-6">

            {{-- Bagian Mahasiswa --}}
            <div>
                <div class="flex items-center">
                    <input id="isStudent" type="checkbox" x-model="isStudent"
                        class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded">
                    <label for="isStudent" class="ml-2 block text-sm text-gray-900 dark:text-gray-200">
                        Apakah penghuni ini Mahasiswa?
                    </label>
                </div>

                <div x-show="isStudent" x-transition class="mt-4 p-4 border border-gray-200 rounded-lg space-y-4">
                    <h4 class="font-semibold text-gray-800 dark:text-gray-100">Detail Kemahasiswaan</h4>
                    {{-- NIM --}}
                    <div>
                        <x-managers.form.label>NIM / ID Mahasiswa</x-managers.form.label>
                        <x-managers.form.input wire:model="studentId" placeholder="Masukkan NIM" />
                    </div>
                    {{-- Fakultas --}}
                    <div>
                        <x-managers.form.label>Fakultas</x-managers.form.label>
                        <x-managers.form.select wire:model.live="faculty" :options="$facultyOptions" label="Pilih Fakultas" />
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {{-- Program Studi --}}
                        <div>
                            <x-managers.form.label>Program Studi</x-managers.form.label>
                            <x-managers.form.select wire:model="studyProgram" :options="$studyProgramOptions"
                                label="Pilih Program Studi" />
                        </div>
                        {{-- Tahun Angkatan --}}
                        <div>
                            <x-managers.form.label>Tahun Angkatan</x-managers.form.label>
                            <x-managers.form.select wire:model="classYear" :options="$classYearOptions"
                                label="Pilih Tahun Angkatan" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-4">
                <x-managers.ui.button type="button" variant="secondary" wire:click="$set('showModal', false)">
                    Batal
                </x-managers.ui.button>
                <x-managers.ui.button type="submit" variant="primary">
                    Simpan Perubahan
                </x-managers.ui.button>
            </div>
        </form>
    </div>
</x-managers.ui.modal>

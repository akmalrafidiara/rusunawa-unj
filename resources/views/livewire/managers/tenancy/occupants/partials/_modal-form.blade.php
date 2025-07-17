{{-- Modal Form --}}
<x-managers.ui.modal title="{{ $occupantIdBeingSelected ? 'Edit' : 'Tambah' }} Penghuni" :show="$showModal && $modalType === 'form'"
    class="max-w-2xl">
    {{--
        Inisialisasi Alpine.js untuk mengelola visibilitas form mahasiswa
        x-data dihubungkan dengan properti Livewire menggunakan @entangle
    --}}
    <div x-data="{ isStudent: @entangle('isStudent') }">
        <form wire:submit.prevent="save" class="space-y-4">
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
                {{-- Nama Lengkap --}}
                <div>
                    <x-managers.form.label>Nomor WhatsApp</x-managers.form.label>
                    <x-managers.form.input wire:model.live="whatsappNumber" placeholder="WhatsApp Number"
                        type="text" />
                </div>
                {{-- Email --}}
                <div>
                    <x-managers.form.label>Jenis Kelamin</x-managers.form.label>
                    <x-managers.form.select wire:model.live="gender" :options="$genderOptions" label="Pilih Jenis Kelamin" />
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
                            <img src="{{ $identityCardFile instanceof \Illuminate\Http\UploadedFile ? $identityCardFile->temporaryUrl() : asset('storage/' . $identityCardFile) }}"
                                alt="Preview Gambar" class="w-16 h-16 object-cover rounded border" />
                        </div>
                    @endif

                    <div class="mb-2">
                        @if ($errors->has('identityCardFile'))
                            <span class="text-red-500 text-sm">{{ $errors->first('identityCardFile') }}</span>
                        @else
                            <x-managers.form.small>Max 2MB. JPG, PNG, GIF</x-managers.form.small>
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
                            <img src="{{ $communityCardFile instanceof \Illuminate\Http\UploadedFile ? $communityCardFile->temporaryUrl() : asset('storage/' . $communityCardFile) }}"
                                alt="Preview Gambar" class="w-16 h-16 object-cover rounded border" />
                        </div>
                    @endif

                    <div class="mb-2">
                        @if ($errors->has('communityCardFile'))
                            <span class="text-red-500 text-sm">{{ $errors->first('communityCardFile') }}</span>
                        @else
                            <x-managers.form.small>Max 2MB. JPG, PNG, GIF</x-managers.form.small>
                        @endif
                    </div>

                    <x-filepond::upload wire:model.live="communityCardFile" />
                </div>
            </div>

            <hr class="my-6">

            {{-- Bagian Kontrak --}}
            <div>
                <x-managers.form.label>Kontrak Terkait</x-managers.form.label>
                <x-managers.form.multiple-select wire:model="contractIds" :options="$contractOptions" />
            </div>

            {{-- Bagian Status & Catatan --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                {{-- Status Penghuni --}}
                <div>
                    <x-managers.form.label>Status Verifikasi</x-managers.form.label>
                    <x-managers.form.select wire:model="status" :options="$statusOptions" label="Pilih Status" />
                </div>
            </div>
            {{-- Catatan --}}
            <div>
                <x-managers.form.label>Catatan (Opsional)</x-managers.form.label>
                <x-managers.form.textarea wire:model="notes" placeholder="Masukkan catatan jika ada" rows="3" />
            </div>

            <hr class="my-6">

            {{-- Bagian Mahasiswa --}}
            <div>
                {{-- Checkbox untuk mengontrol visibilitas form mahasiswa --}}
                <div class="flex items-center">
                    <input id="isStudent" type="checkbox" x-model="isStudent"
                        class="h-4 w-4 text-emerald-600 focus:ring-emerald-500 border-gray-300 rounded">
                    <label for="isStudent" class="ml-2 block text-sm text-gray-900 dark:text-gray-200">
                        Apakah penghuni ini Mahasiswa?
                    </label>
                </div>

                {{-- Form ini hanya akan muncul jika 'isStudent' bernilai true --}}
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
                            <x-managers.form.select wire:model.live="studyProgram" :options="$studyProgramOptions ?? []"
                                label="Pilih Program Studi" />
                        </div>
                        {{-- Tahun Angkatan --}}
                        <div>
                            <x-managers.form.label>Tahun Angkatan</x-managers.form.label>
                            <x-managers.form.select wire:model.live="classYear" :options="$classYearOptions"
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

{{-- Modal Form --}}
<x-managers.ui.modal title="Form Tipe Kamar" :show="$showModal" class="max-w-md">
    <form wire:submit.prevent="save" class="space-y-4">
        <!-- Nama Tipe -->
        <x-managers.form.label>Nama Tipe</x-managers.form.label>
        <x-managers.form.input wire:model.live="name" placeholder="Contoh: Studio, 1 Kamar, Loft" />

        <!-- Deskripsi -->
        <x-managers.form.label>Deskripsi Tipe</x-managers.form.label>
        <x-managers.form.textarea wire:model.live="description" rows="3" />

        <!-- Facilities (Array) -->
        <div>
            <x-managers.form.label>Fasilitas</x-managers.form.label>
            <!-- Daftar Fasilitas -->
            @if (!empty($facilities))
                @foreach ($facilities as $index => $facility)
                    <div class="flex items-center gap-2 mb-2" wire:key="facility-{{ $index }}">
                        <x-managers.form.input wire:model.live="facilities.{{ $index }}"
                            placeholder="Contoh: AC, Dapur" />
                        <x-managers.ui.button wire:click="removeFacility({{ $index }})" variant="danger"
                            size="sm" icon="trash" />
                    </div>
                @endforeach
            @else
                <p class="text-gray-500 dark:text-gray-400 text-sm">Belum ada fasilitas.</p>
            @endif

            <!-- Tambah Fasilitas Baru -->
            <div class="mt-3 flex gap-2">
                <x-managers.form.input wire:model.live="newFacility" placeholder="Masukkan fasilitas baru..." />
                <x-managers.ui.button wire:click="addFacility()" variant="secondary" size="sm" icon="plus">
                    Tambah
                </x-managers.ui.button>
            </div>
        </div>

        <!-- Upload Gambar -->
        <x-managers.form.label>Gambar Tipe</x-managers.form.label>
        @if ($image)
            <div class="inline-flex gap-2 border border-gray-300 rounded p-2 mb-2">
                <x-managers.form.small>Preview</x-managers.form.small>
                <img src="{{ $image instanceof \Illuminate\Http\UploadedFile ? $image->temporaryUrl() : asset('storage/' . $image) }}"
                    alt="Preview Gambar" class="w-16 h-16 object-cover rounded border" />
            </div>
        @endif

        <div class="mb-2">
            @if ($errors->has('image'))
                <span class="text-red-500 text-sm">{{ $errors->first('image') }}</span>
            @else
                <x-managers.form.small>Max 2MB. JPG, PNG, GIF</x-managers.form.small>
            @endif
        </div>

        <x-filepond::upload wire:model.live="image" />


        <!-- Tombol Aksi -->
        <div class="flex justify-end gap-2 mt-10">
            <x-managers.ui.button type="button" variant="secondary"
                wire:click="$set('showModal', false)">Batal</x-managers.ui.button>
            <x-managers.ui.button wire:click="save()" variant="primary">
                Simpan
            </x-managers.ui.button>
        </div>
    </form>
</x-managers.ui.modal>

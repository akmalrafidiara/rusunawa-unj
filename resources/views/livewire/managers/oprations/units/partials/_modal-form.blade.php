{{-- Modal Form --}}
<x-managers.ui.modal title="Form Unit" :show="$showModal && $modalType === 'form'" class="max-w-md">
    <form wire:submit.prevent="save" class="space-y-4">
        <!-- Room Number -->
        <x-managers.form.label>Nomor Kamar</x-managers.form.label>
        <x-managers.form.input wire:model.live="roomNumber" placeholder="Contoh: A101" />

        <!-- Capacity -->
        <x-managers.form.label>Kapasitas</x-managers.form.label>
        <x-managers.form.input wire:model.live="capacity" type="number" placeholder="Max: 3" />

        <!-- Virtual Account Number -->
        <x-managers.form.label>Nomor Virtual Account</x-managers.form.label>
        <x-managers.form.input wire:model.live="virtualAccountNumber" placeholder="Contoh: 008" type="number" />

        <!-- Gender Allowed -->
        <x-managers.form.label>Peruntukan</x-managers.form.label>
        <x-managers.form.select wire:model.live="genderAllowed" :options="$genderAllowedOptions" label="Pilih Peruntukan" />

        <!-- Status -->
        <x-managers.form.label>Status Unit</x-managers.form.label>
        <x-managers.form.select wire:model.live="status" :options="$statusOptions" label="Pilih Status" />

        <!-- Unit Type -->
        <x-managers.form.label>Tipe Unit</x-managers.form.label>
        <x-managers.form.select wire:model.live="unitTypeId" :options="$unitTypeOptions" label="Pilih Tipe Unit" />

        <!-- Unit Cluster -->
        <x-managers.form.label>Cluster Unit</x-managers.form.label>
        <x-managers.form.select wire:model.live="unitClusterId" :options="$unitClusterOptions" label="Pilih Cluster Unit" />

        {{-- Unit Images --}}
        <x-managers.form.label>Gambar Unit</x-managers.form.label>

        {{-- Existing Images while Editing --}}
        @if ($existingImages && count($existingImages) > 0)
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2 border border-gray-300 rounded p-2 mb-2">
                <x-managers.form.small class="col-span-full">Gambar Saat Ini</x-managers.form.small>
                @foreach ($existingImages as $image)
                    <div class="relative" wire:key="existing-image-{{ $image['id'] }}">
                        <img src="{{ asset('storage/' . $image['path']) }}" alt="Gambar {{ $image['id'] }}"
                            class="w-full h-16 object-cover rounded border" />

                        <button type="button" wire:click="queueImageForDeletion({{ $image['id'] }})"
                            wire:loading.attr="disabled" wire:target="queueImageForDeletion({{ $image['id'] }})"
                            class="absolute -top-1 -right-1 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600">
                            <flux:icon name="x-mark" class="w-3 h-3" />
                        </button>
                    </div>
                @endforeach
            </div>
        @endif

        <div wire:key="filepond-wrapper">
            <x-filepond::upload wire:model.live="unitImages" multiple accept="image/png, image/jpeg, image/gif"
                max-file-size="2MB" />
        </div>


        {{-- Pesan error dan petunjuk --}}
        <div class="mt-2">
            @error('unitImages.*')
                {{-- Error ini akan ditampilkan jika validasi di server gagal --}}
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @else
                <x-managers.form.small>Max 2MB per file. Tipe: JPG, PNG, GIF. Bisa upload banyak
                    gambar.</x-managers.form.small>
            @enderror
        </div>


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

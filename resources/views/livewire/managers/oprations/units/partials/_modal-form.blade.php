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

        {{-- Unit Rates --}}
        <x-managers.form.label>Tarif Unit</x-managers.form.label>
        <div class="space-y-2">
            @if ($rateOptions && count($rateOptions) > 0)
                @foreach ($rateOptions as $rate)
                    <div class="flex items-center space-x-2">

                        <input type="checkbox" wire:model.live="unitRatesId" value="{{ $rate['value'] }}"
                            id="rate-{{ $rate['value'] }}"
                            class="rounded border-gray-300 dark:border-gray-600 text-blue-600 dark:text-blue-400 bg-white dark:bg-gray-700 shadow-sm focus:border-blue-300 dark:focus:border-blue-500 focus:ring focus:ring-blue-200 dark:focus:ring-blue-800 focus:ring-opacity-50">

                        <label for="rate-{{ $rate['value'] }}" class="text-sm text-gray-700 dark:text-gray-300">
                            {{ $rate['label'] }}
                        </label>
                    </div>
                @endforeach
            @else
                <x-managers.form.small class="text-gray-500 dark:text-gray-400">
                    Tidak ada tarif tersedia
                </x-managers.form.small>
            @endif
        </div>

        {{-- Unit Notes --}}
        <x-managers.form.label>Keterangan Unit</x-managers.form.label>
        <x-managers.form.textarea wire:model.live="notes" placeholder="Masukkan keterangan unit di sini"
            rows="3" />

        <!-- Upload Gambar -->
        <x-managers.form.label>Gambar Unit</x-managers.form.label>
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

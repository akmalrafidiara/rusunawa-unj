<div class="flex flex-col gap-6">
    <!-- Toolbar -->
    <div class="flex flex-col sm:flex-row gap-4">

        {{-- Search Form --}}
        <x-managers.form.input wire:model.live="search" clearable placeholder="Cari tipe unit..." icon="magnifying-glass"
            class="w-full" />

        <div class="flex gap-4">
            {{-- Add Unit Type Button --}}
            <x-managers.ui.button wire:click="create" variant="primary" icon="plus" class="w-full sm:w-auto">
                Tambah Tipe Unit
            </x-managers.ui.button>

            {{-- Dropdown for Filters --}}
            <x-managers.ui.dropdown class="flex flex-col gap-2">
                <x-slot name="trigger">
                    <flux:icon.adjustments-horizontal />
                </x-slot>
                @php
                    $orderByOptions = [
                        ['value' => 'name', 'label' => 'Nama'],
                        ['value' => 'created_at', 'label' => 'Tanggal'],
                    ];

                    $sortOptions = [['value' => 'asc', 'label' => 'Menaik'], ['value' => 'desc', 'label' => 'Menurun']];
                @endphp

                {{-- Sort Filter --}}
                <x-managers.form.small>Urutkan</x-managers.form.small>
                <div class="flex gap-2">
                    <x-managers.ui.dropdown-picker wire:model.live="orderBy" :options="$orderByOptions"
                        label="Urutkan Berdasarkan" wire:key="dropdown-order-by" disabled />

                    <x-managers.ui.dropdown-picker wire:model.live="sort" :options="$sortOptions" label="Sort"
                        wire:key="dropdown-sort" disabled />
                </div>
            </x-managers.ui.dropdown>
        </div>
    </div>

    <!-- Tabel Data -->
    <x-managers.ui.card class="p-0">
        <x-managers.table.table :headers="['Nama', 'Deskripsi', 'Gambar', 'Fasilitas', 'Aksi']">
            <x-managers.table.body>
                @forelse ($unitTypes as $unitType)
                    <x-managers.table.row wire:key="{{ $unitType->id }}">
                        <!-- Nama -->
                        <x-managers.table.cell>
                            <span class="font-bold">{{ $unitType->name }}</span>
                        </x-managers.table.cell>

                        <!-- Description -->
                        <x-managers.table.cell>{{ $unitType->description }}</x-managers.table.cell>

                        {{-- Image --}}
                        <x-managers.table.cell>
                            @if ($unitType->image)
                                <img src="{{ asset('storage/' . $unitType->image) }}" alt="{{ $unitType->name }}"
                                    class="w-16 h-16 object-cover rounded">
                            @else
                                <span class="text-gray-500">Tidak ada gambar</span>
                            @endif
                        </x-managers.table.cell>

                        {{-- Facilities --}}
                        <x-managers.table.cell>
                            @if (!empty($unitType->facilities))
                                <ul class="list-disc pl-5 space-y-1">
                                    @foreach ($unitType->facilities as $facility)
                                        <li>{{ $facility }}</li>
                                    @endforeach
                                </ul>
                            @else
                                <span class="text-gray-500">Tidak tersedia</span>
                            @endif
                        </x-managers.table.cell>

                        <!-- Aksi -->
                        <x-managers.table.cell class="text-right">
                            <div class="flex gap-2">
                                {{-- Edit Button --}}
                                <x-managers.ui.button wire:click="edit({{ $unitType->id }})" variant="secondary"
                                    size="sm">
                                    <flux:icon.pencil class="w-4" />
                                </x-managers.ui.button>

                                {{-- Delete Button --}}
                                <x-managers.ui.button wire:click="confirmDelete({{ $unitType }})" id="delete-user"
                                    variant="danger" size="sm">
                                    <flux:icon.trash class="w-4" />
                                </x-managers.ui.button>
                            </div>
                        </x-managers.table.cell>
                    </x-managers.table.row>
                @empty
                    <x-managers.table.row>
                        <x-managers.table.cell colspan="5" class="text-center text-gray-500">
                            Tidak ada data pengguna ditemukan.
                        </x-managers.table.cell>
                    </x-managers.table.row>
                @endforelse
            </x-managers.table.body>
        </x-managers.table.table>
    </x-managers.ui.card>

    {{-- Modal Create --}}
    <x-managers.ui.modal title="Form Tipe Kamar" :show="$showModal">
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
</div>

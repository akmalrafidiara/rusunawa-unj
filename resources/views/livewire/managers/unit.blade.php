<div class="flex flex-col gap-6">

    <!-- Toolbar -->
    <div class="flex flex-col sm:flex-row gap-4">

        {{-- Search Form --}}
        <x-managers.form.input wire:model.live="search" clearable placeholder="Cari cluster unit..."
            icon="magnifying-glass" class="w-full" />

        <div class="flex gap-4">
            {{-- Add Unit Type Button --}}
            <x-managers.ui.button wire:click="create" variant="primary" icon="plus" class="w-full sm:w-auto">
                Tambah Cluster Unit
            </x-managers.ui.button>

            {{-- Dropdown for Filters --}}
            <x-managers.ui.dropdown class="flex flex-col gap-2">
                <x-slot name="trigger">
                    <flux:icon.adjustments-horizontal />
                </x-slot>
                @php
                    $orderByOptions = [
                        ['value' => 'room_number', 'label' => 'No Kamar'],
                        ['value' => 'capacity', 'label' => 'Kapasitas'],
                        ['value' => 'virtual_account_number', 'label' => 'No VA'],
                        ['value' => 'created_at', 'label' => 'Tanggal'],
                    ];

                    $sortOptions = [['value' => 'asc', 'label' => 'Menaik'], ['value' => 'desc', 'label' => 'Menurun']];
                @endphp

                <x-managers.form.small>Filter</x-managers.form.small>
                <div class="flex gap-2">
                    <x-managers.ui.dropdown-picker wire:model.live="statusFilter" :options="$statusOptions" label="Semua Status"
                        wire:key="dropdown-status" />

                    <x-managers.ui.dropdown-picker wire:model.live="genderAllowedFilter" :options="$genderAllowedOptions"
                        label="Semua Peruntukan" wire:key="dropdown-gender-allowed" />

                    <x-managers.ui.dropdown-picker wire:model.live="unitTypeFilter" :options="$unitTypeOptions"
                        label="Semua Tipe Unit" wire:key="dropdown-unit-type" />

                    <x-managers.ui.dropdown-picker wire:model.live="unitClusterFilter" :options="$unitClusterOptions"
                        label="Semua Cluster Unit" wire:key="dropdown-unit-cluster" />
                </div>

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

    {{-- Panel --}}
    <div class="flex justify-end items-center gap-2 w-full sm:w-auto">
        {{-- Per Page Input --}}
        <span>Baris</span>
        <div class="w-22">
            <x-managers.form.input wire:model.live="perPage" type="number" placeholder="15" />
        </div>

        {{-- Export Button --}}
        <span>Unduh</span>
        <x-managers.ui.button-export />
    </div>

    <!-- Tabel Data -->
    <x-managers.ui.card class="p-0">
        <x-managers.table.table :headers="[
            'No Kamar',
            'Kapasitas',
            'No VA (Mandiri)',
            'Peruntukan',
            'Status',
            'Tipe Unit',
            'Cluster Unit',
            'Aksi',
        ]">
            <x-managers.table.body>
                @forelse ($units as $unit)
                    <x-managers.table.row wire:key="{{ $unit->id }}">
                        <!-- Room Number -->
                        <x-managers.table.cell>
                            <span class="font-bold">{{ $unit->room_number }}</span>
                        </x-managers.table.cell>

                        {{-- Capacity --}}
                        <x-managers.table.cell>{{ $unit->capacity }}</x-managers.table.cell>

                        {{-- Virtual Account Number --}}
                        <x-managers.table.cell>
                            <span
                                class="bg-gray-200 font-mono p-2">{{ chunk_split($unit->virtual_account_number, 4, ' ') }}</span>
                        </x-managers.table.cell>

                        <!-- Gender Allowed -->
                        <x-managers.table.cell>
                            @foreach ($unit->gender_allowed as $gender_allowed)
                                @php
                                    $genderAllowedEnum = \App\Enums\GenderAllowed::tryFrom($gender_allowed);
                                @endphp

                                <x-managers.ui.badge :type="$genderAllowedEnum?->value ?? 'default'" :color="$genderAllowedEnum?->color()">
                                    {{ $genderAllowedEnum?->label() }}
                                </x-managers.ui.badge>
                            @endforeach
                        </x-managers.table.cell>

                        {{-- Status --}}
                        <x-managers.table.cell>
                            @foreach ($unit->status as $status)
                                @php
                                    $statusEnum = \App\Enums\UnitStatus::tryFrom($status);
                                @endphp

                                <x-managers.ui.badge :type="$statusEnum?->value ?? 'default'" :color="$statusEnum?->color()">
                                    {{ $statusEnum?->label() }}
                                </x-managers.ui.badge>
                            @endforeach
                        </x-managers.table.cell>

                        {{-- Unit Type --}}
                        <x-managers.table.cell>
                            @if ($unit->unitType)
                                <span class="font-semibold">{{ $unit->unitType->name }}</span>
                            @else
                                <span class="text-gray-500">Tidak ada tipe unit</span>
                            @endif
                        </x-managers.table.cell>

                        {{-- Cluster Unit --}}
                        <x-managers.table.cell>
                            @if ($unit->unitCluster)
                                <span class="font-semibold">{{ $unit->unitCluster->name }}</span>
                            @else
                                <span class="text-gray-500">Tidak ada cluster unit</span>
                            @endif
                        </x-managers.table.cell>

                        <!-- Aksi -->
                        <x-managers.table.cell class="text-right">
                            <div class="flex gap-2">
                                {{-- Detail Button --}}
                                <x-managers.ui.button wire:click="detail({{ $unit->id }})" variant="info"
                                    size="sm">
                                    <flux:icon.eye class="w-4" />
                                </x-managers.ui.button>

                                {{-- Edit Button --}}
                                <x-managers.ui.button wire:click="edit({{ $unit->id }})" variant="secondary"
                                    size="sm">
                                    <flux:icon.pencil class="w-4" />
                                </x-managers.ui.button>

                                {{-- Delete Button --}}
                                <x-managers.ui.button wire:click="confirmDelete({{ $unit }})" id="delete-user"
                                    variant="danger" size="sm">
                                    <flux:icon.trash class="w-4" />
                                </x-managers.ui.button>
                            </div>
                        </x-managers.table.cell>
                    </x-managers.table.row>
                @empty
                    <x-managers.table.row>
                        <x-managers.table.cell colspan="5" class="text-center text-gray-500">
                            Tidak ada data unit ditemukan.
                        </x-managers.table.cell>
                    </x-managers.table.row>
                @endforelse
            </x-managers.table.body>
        </x-managers.table.table>

        {{-- Pagination --}}
        <x-managers.ui.pagination :paginator="$units" />
    </x-managers.ui.card>

    {{-- Modal Create --}}
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

    {{-- Modal Detail --}}
    @if ($unitIdBeingEdited)
        <x-managers.ui.modal title="Detail Cluster Unit" :show="$showModal && $modalType === 'detail'" class="max-w-3xl">
            <div class="space-y-6">
                {{-- Unit Header --}}
                <div
                    class="flex items-center gap-4 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg border border-blue-100">
                    <div class="p-3 bg-blue-500 rounded-full">
                        <flux:icon.home class="w-6 h-6 text-white" />
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-800">Unit {{ $roomNumber }}</h3>
                        <p class="text-sm text-gray-600">Detail informasi unit rusunawa</p>
                    </div>
                </div>

                {{-- Unit Images --}}
                @if (!empty($existingImages))
                    <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                            <flux:icon.photo class="w-5 h-5 text-purple-500" />
                            Gambar Unit
                        </h4>
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                            @foreach ($existingImages as $index => $image)
                                <div class="relative group">
                                    <img src="{{ $image instanceof \Illuminate\Http\UploadedFile ? $image->temporaryUrl() : asset('storage/' . $image->path) }}"
                                        alt="Gambar Unit {{ $index + 1 }}"
                                        class="w-full h-24 object-cover rounded-lg border border-gray-200 hover:shadow-md transition-shadow cursor-pointer">
                                    <div
                                        class="absolute inset-0 bg-opacity-0 group-hover:bg-opacity-20 rounded-lg transition-opacity">
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Unit Information Grid --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Basic Info Card --}}
                    <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                            <flux:icon.information-circle class="w-5 h-5 text-blue-500" />
                            Informasi Dasar
                        </h4>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-gray-600">Nomor Kamar</span>
                                <span class="font-semibold text-lg text-blue-600">{{ $roomNumber }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-gray-600">Kapasitas</span>
                                <div class="flex items-center gap-1">
                                    <flux:icon.users class="w-4 h-4 text-gray-500" />
                                    <span class="font-semibold">{{ $capacity }} Orang</span>
                                </div>
                            </div>
                            <div class="flex justify-between items-center py-2">
                                <span class="text-gray-600">Virtual Account</span>
                                <span class="font-mono text-sm bg-gray-100 px-2 py-1 rounded">
                                    {{ chunk_split($virtualAccountNumber, 4, ' ') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Status & Classification Card --}}
                    <div class="bg-white border border-gray-200 rounded-lg p-4 shadow-sm">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                            <flux:icon.tag class="w-5 h-5 text-green-500" />
                            Status & Klasifikasi
                        </h4>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-gray-600">Peruntukan</span>
                                @php
                                    $genderEnum = \App\Enums\GenderAllowed::tryFrom($genderAllowed);
                                @endphp
                                <x-managers.ui.badge :type="$genderEnum?->value ?? 'default'" :color="$genderEnum?->color()">
                                    {{ $genderEnum?->label() }}
                                </x-managers.ui.badge>
                            </div>
                            <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                <span class="text-gray-600">Status Unit</span>
                                @php
                                    $statusEnum = \App\Enums\UnitStatus::tryFrom($status);
                                @endphp
                                <x-managers.ui.badge :type="$statusEnum?->value ?? 'default'" :color="$statusEnum?->color()">
                                    {{ $statusEnum?->label() }}
                                </x-managers.ui.badge>
                            </div>
                            <div class="flex justify-between items-center py-2">
                                <span class="text-gray-600">Tipe Unit</span>
                                <span class="font-semibold">{{ $unitTypeName ?? 'Tidak ada tipe' }}</span>
                            </div>
                            <div class="flex justify-between items-center py-2">
                                <span class="text-gray-600">Cluster Unit</span>
                                <span class="font-semibold">{{ $unitClusterName ?? 'Tidak ada cluster' }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Timestamps Card --}}
                <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
                    <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <flux:icon.clock class="w-5 h-5 text-gray-500" />
                        Informasi Waktu
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Tanggal Dibuat</span>
                            <span class="font-medium">
                                @if ($createdAt)
                                    {{ $createdAt->format('d M Y, H:i') }} WIB
                                @else
                                    -
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Terakhir Diperbarui</span>
                            <span class="font-medium">
                                @if ($updatedAt)
                                    {{ $updatedAt->format('d M Y, H:i') }} WIB
                                @else
                                    -
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end mt-6">
                <x-managers.ui.button type="button" variant="danger" wire:click="$set('showModal', false)">
                    Tutup
                </x-managers.ui.button>
            </div>
        </x-managers.ui.modal>
    @endif

</div>

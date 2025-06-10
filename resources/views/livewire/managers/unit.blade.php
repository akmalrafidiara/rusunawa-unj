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
                            @php
                                $genderAllowedEnum = \App\Enums\GenderAllowed::tryFrom($unit->gender_allowed);
                            @endphp

                            <x-managers.ui.badge :type="$genderAllowedEnum?->value ?? 'default'" :color="$genderAllowedEnum?->color()">
                                {{ $genderAllowedEnum?->label() }}
                            </x-managers.ui.badge>
                        </x-managers.table.cell>

                        {{-- Status --}}
                        <x-managers.table.cell>
                            @php
                                $statusEnum = \App\Enums\UnitStatus::tryFrom($unit->status);
                            @endphp

                            <x-managers.ui.badge :type="$statusEnum?->value ?? 'default'" :color="$statusEnum?->color()">
                                {{ $statusEnum?->label() }}
                            </x-managers.ui.badge>
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
    {{-- <x-managers.ui.modal title="Form Tipe Kamar" :show="$showModal && $modalType === 'form'">
        <form wire:submit.prevent="save" class="space-y-4">
            <!-- Nama Tipe -->
            <x-managers.form.label>Nama Cluster</x-managers.form.label>
            <x-managers.form.input wire:model.live="name" placeholder="Contoh: Gedung A.." />

            <!-- PIC (Staff) -->
            <x-managers.form.label>Staff Penanggung Jawab</x-managers.form.label>
            <x-managers.form.select wire:model.live="staffId" :options="$staffOptions" label="Pilih Staff" />

            <!-- Alamat -->
            <x-managers.form.label>Alamat Cluster</x-managers.form.label>
            <x-managers.form.input wire:model.live="address" placeholder="Contoh: Jl. Raya No. 123" />

            <!-- Deskripsi -->
            <x-managers.form.label>Deskripsi Tipe</x-managers.form.label>
            <x-managers.form.textarea wire:model.live="description" rows="3" />

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
    </x-managers.ui.modal> --}}

    {{-- Modal Detail --}}
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
                            <span class="font-semibold">{{ $unitType?->name ?? 'Tidak ada tipe' }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2">
                            <span class="text-gray-600">Cluster Unit</span>
                            <span class="font-semibold">{{ $unitCluster?->name ?? 'Tidak ada cluster' }}</span>
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
</div>

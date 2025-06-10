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
                        ['value' => 'price', 'label' => 'Harga'],
                        ['value' => 'occupant_type', 'label' => 'Tipe Penghuni'],
                        ['value' => 'created_at', 'label' => 'Tanggal'],
                    ];

                    $sortOptions = [['value' => 'asc', 'label' => 'Menaik'], ['value' => 'desc', 'label' => 'Menurun']];
                @endphp
                {{-- Filter --}}
                <x-managers.form.small>Filter</x-managers.form.small>
                <div class="flex gap-2">
                    <x-managers.ui.dropdown-picker wire:model.live="pricingBasisFilter" :options="$pricingBasisOptions"
                        label="Semua Dasar Harga" wire:key="dropdown-role" />
                </div>

                {{-- Sort --}}
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
        <x-managers.table.table :headers="['Harga', 'Tipe Penghuni', 'Dasar Penetapan Harga', 'Aksi']">
            <x-managers.table.body>
                @forelse ($unitRates as $unitRate)
                    <x-managers.table.row wire:key="{{ $unitRate->id }}">
                        <!-- Price -->
                        <x-managers.table.cell>
                            <span class="font-bold">{{ $unitRate->formatted_price }}</span>
                        </x-managers.table.cell>

                        <!-- Occupant Type -->
                        <x-managers.table.cell>{{ $unitRate->occupant_type }}</x-managers.table.cell>

                        {{-- Pricing Bases --}}
                        <x-managers.table.cell>
                            @foreach ($unitRate->pricing_basis as $pricing_basis)
                                @php
                                    $pricingBasisEnum = \App\Enums\PricingBasis::tryFrom($pricing_basis);
                                @endphp

                                <x-managers.ui.badge :type="$pricingBasisEnum?->value ?? 'default'" :color="$pricingBasisEnum?->color()">
                                    {{ $pricingBasisEnum?->label() }}
                                </x-managers.ui.badge>
                            @endforeach
                        </x-managers.table.cell>

                        <!-- Aksi -->
                        <x-managers.table.cell class="text-right">
                            <div class="flex gap-2">
                                {{-- Edit Button --}}
                                <x-managers.ui.button wire:click="edit({{ $unitRate->id }})" variant="secondary"
                                    size="sm">
                                    <flux:icon.pencil class="w-4" />
                                </x-managers.ui.button>

                                {{-- Delete Button --}}
                                <x-managers.ui.button wire:click="confirmDelete({{ $unitRate }})" id="delete-user"
                                    variant="danger" size="sm">
                                    <flux:icon.trash class="w-4" />
                                </x-managers.ui.button>
                            </div>
                        </x-managers.table.cell>
                    </x-managers.table.row>
                @empty
                    <x-managers.table.row>
                        <x-managers.table.cell colspan="5" class="text-center text-gray-500">
                            Tidak ada data rate unit ditemukan.
                        </x-managers.table.cell>
                    </x-managers.table.row>
                @endforelse
            </x-managers.table.body>
        </x-managers.table.table>
    </x-managers.ui.card>

    {{-- Modal Create --}}
    <x-managers.ui.modal title="Form Tipe Kamar" :show="$showModal">
        <form wire:submit.prevent="save" class="space-y-4">
            <!-- Price -->
            <x-managers.form.label>Price</x-managers.form.label>
            <x-managers.form.input wire:model.live="price" placeholder="Contoh: Rp175.000" rupiah />

            <!-- Occupant Type Tipe -->
            <x-managers.form.label>Tipe Penghuni</x-managers.form.label>
            <x-managers.form.input wire:model.live="occupantType" placeholder="Contoh: Internal UNJ" icon="user" />

            <!-- Pricing Base -->
            <x-managers.form.label>Dasar Penetapan Harga</x-managers.form.label>
            <x-managers.form.select wire:model.live="pricingBasis" :options="$pricingBasisOptions" label="Basis Harga" />

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

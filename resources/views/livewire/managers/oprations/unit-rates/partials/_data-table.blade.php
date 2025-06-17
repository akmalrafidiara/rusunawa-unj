<!-- Tabel Data -->
<x-managers.ui.card class="p-0">
    <x-managers.table.table :headers="['Harga', 'Tipe Penghuni', 'Dasar Penetapan Harga', 'Verifikasi', 'Aksi']">
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

                            <x-managers.ui.badge :color="$pricingBasisEnum?->color()">
                                {{ $pricingBasisEnum?->label() }}
                            </x-managers.ui.badge>
                        @endforeach
                    </x-managers.table.cell>

                    <!-- Requires Verification -->
                    <x-managers.table.cell>
                        @if ($unitRate->requires_verification)
                            <x-managers.ui.badge type="success">Ya</x-managers.ui.badge>
                        @else
                            <x-managers.ui.badge type="danger">Tidak</x-managers.ui.badge>
                        @endif
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

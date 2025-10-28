<!-- Tabel Data -->
<x-managers.ui.card class="p-0">
    <x-managers.table.table :headers="['No Kamar', 'Tipe Unit', 'Kapasitas', 'No VA (Mandiri)', 'Peruntukan', 'Status', 'Keterangan', 'Aksi']">
        <x-managers.table.body>
            @forelse ($units as $unit)
                <x-managers.table.row wire:key="{{ $unit->id }}">
                    <!-- Room Number -->
                    <x-managers.table.cell>
                        @if ($unit->unitCluster)
                            <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">
                                {{ $unit->unitCluster->name }}
                            </div>
                        @endif
                        <span class="font-bold text-2xl">{{ $unit->room_number }}</span>
                    </x-managers.table.cell>

                    {{-- Unit Type --}}
                    <x-managers.table.cell>
                        @if ($unit->unitType)
                            <span class="font-semibold">{{ $unit->unitType->name }}</span>
                        @else
                            <span class="text-gray-500">Tidak ada tipe unit</span>
                        @endif
                    </x-managers.table.cell>

                    {{-- Capacity --}}
                    <x-managers.table.cell>{{ $unit->capacity }}</x-managers.table.cell>

                    {{-- Virtual Account Number --}}
                    <x-managers.table.cell>
                        <span
                            class="bg-gray-200 dark:bg-zinc-700 font-mono p-2 text-gray-900 dark:text-gray-100 whitespace-nowrap">{{ chunk_split($unit->virtual_account_number, 4, ' ') }}</span>
                    </x-managers.table.cell>

                    <!-- Gender Allowed -->
                    <x-managers.table.cell>
                        @foreach ($unit->gender_allowed as $gender_allowed)
                            @php
                                $genderAllowedEnum = \App\Enums\GenderAllowed::tryFrom($gender_allowed);
                            @endphp

                            <x-managers.ui.badge :color="$genderAllowedEnum?->color()">
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

                            <x-managers.ui.badge :color="$statusEnum?->color()">
                                {{ $statusEnum?->label() }}
                            </x-managers.ui.badge>
                        @endforeach
                    </x-managers.table.cell>

                    {{-- Unit Notes --}}
                    <x-managers.table.cell>
                        <span class="text-gray-600 dark:text-gray-400">{{ $unit->notes ?: '-' }}</span>
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

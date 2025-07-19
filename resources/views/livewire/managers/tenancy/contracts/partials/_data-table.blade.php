<x-managers.ui.card class="p-0">
    <x-managers.table.table :headers="['Kode Kontrak', 'Penyewa Utama', 'Unit', 'Total Harga', 'Jangka Waktu', 'Status', 'Aksi']">
        <x-managers.table.body>
            @forelse ($contracts as $contract)
                <x-managers.table.row wire:key="{{ $contract->id }}">
                    {{-- Contract Code --}}
                    <x-managers.table.cell>
                        <span class="font-medium text-gray-900 dark:text-gray-100">
                            {{ $contract->contract_code }}
                        </span>
                    </x-managers.table.cell>

                    {{-- Primary Occupant Name --}}
                    <x-managers.table.cell>
                        @if ($contract->occupants->isNotEmpty())
                            <span class="text-gray-600 dark:text-gray-400">
                                {{ $contract->occupants->first()->full_name }}
                            </span>
                        @else
                            <span class="text-gray-400 italic">-</span>
                        @endif
                    </x-managers.table.cell>

                    {{-- Unit --}}
                    <x-managers.table.cell>
                        @if ($contract->unit)
                            <span class="text-gray-600 dark:text-gray-400">
                                {{ $contract->unit->unitCluster->name ?? 'N/A' }} |
                                {{ $contract->unit->room_number ?? 'N/A' }}
                            </span>
                        @else
                            <span class="text-gray-400 italic">-</span>
                        @endif
                    </x-managers.table.cell>

                    {{-- Total Price --}}
                    <x-managers.table.cell>
                        <span class="text-gray-600 dark:text-gray-400">
                            Rp {{ number_format($contract->total_price, 0, ',', '.') }}
                        </span>
                    </x-managers.table.cell>

                    {{-- Contract Period --}}
                    <x-managers.table.cell>
                        <span class="text-gray-600 dark:text-gray-400">
                            {{ $contract->start_date->translatedFormat('d M Y') }} -
                            {{ $contract->end_date->translatedFormat('d M Y') }}
                        </span>
                    </x-managers.table.cell>

                    {{-- Status --}}
                    <x-managers.table.cell>
                        <x-managers.ui.badge :color="$contract->status->color()">
                            {{ $contract->status->label() }}
                        </x-managers.ui.badge>
                    </x-managers.table.cell>

                    <x-managers.table.cell class="text-right">
                        <div class="flex gap-2">
                            {{-- Detail Button --}}
                            <x-managers.ui.button wire:click="detail({{ $contract->id }})" variant="info"
                                size="sm">
                                <flux:icon.eye class="w-4" />
                            </x-managers.ui.button>

                            {{-- Edit Button --}}
                            <x-managers.ui.button wire:click="edit({{ $contract->id }})" variant="secondary"
                                size="sm">
                                <flux:icon.pencil class="w-4" />
                            </x-managers.ui.button>

                            {{-- Delete Button --}}
                            {{-- <x-managers.ui.button wire:click="confirmDelete({{ $contract->id }})" id="delete-contract"
                                variant="danger" size="sm">
                                <flux:icon.trash class="w-4" />
                            </x-managers.ui.button> --}}
                        </div>
                    </x-managers.table.cell>
                </x-managers.table.row>
            @empty
                <x-managers.table.row>
                    <x-managers.table.cell colspan="7" class="text-center text-gray-500">
                        Tidak ada data kontrak ditemukan.
                    </x-managers.table.cell>
                </x-managers.table.row>
            @endforelse
        </x-managers.table.body>
    </x-managers.table.table>

    {{-- Pagination --}}
    <x-managers.ui.pagination :paginator="$contracts" />
</x-managers.ui.card>

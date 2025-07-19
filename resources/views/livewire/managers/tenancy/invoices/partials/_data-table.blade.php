<x-managers.ui.card class="p-0">
    <x-managers.table.table :headers="['Nomor Invoice', 'Penyewa', 'Unit', 'Jumlah', 'Tanggal Jatuh Tempo', 'Status', 'Aksi']">
        <x-managers.table.body>
            @forelse ($invoices as $invoice)
                <x-managers.table.row wire:key="{{ $invoice->id }}">
                    {{-- Invoice Number --}}
                    <x-managers.table.cell>
                        <span class="font-medium text-gray-900 dark:text-gray-100">
                            {{ $invoice->invoice_number }}
                        </span>
                    </x-managers.table.cell>

                    {{-- Occupant Name --}}
                    <x-managers.table.cell>
                        @if ($invoice->contract && $invoice->contract->occupants->isNotEmpty())
                            <span class="text-gray-600 dark:text-gray-400">
                                {{ $invoice->contract->occupants->first()->full_name }}
                            </span>
                        @else
                            <span class="text-gray-400 italic">-</span>
                        @endif
                    </x-managers.table.cell>

                    {{-- Unit --}}
                    <x-managers.table.cell>
                        @if ($invoice->contract && $invoice->contract->unit)
                            <span class="text-gray-600 dark:text-gray-400">
                                {{ $invoice->contract->unit->unitCluster->name ?? 'N/A' }} |
                                {{ $invoice->contract->unit->room_number ?? 'N/A' }}
                            </span>
                        @else
                            <span class="text-gray-400 italic">-</span>
                        @endif
                    </x-managers.table.cell>

                    {{-- Amount --}}
                    <x-managers.table.cell>
                        <span class="text-gray-600 dark:text-gray-400">
                            Rp {{ number_format($invoice->amount, 0, ',', '.') }}
                        </span>
                    </x-managers.table.cell>

                    {{-- Due Date --}}
                    <x-managers.table.cell>
                        <span class="text-gray-600 dark:text-gray-400">
                            {{ $invoice->due_at->translatedFormat('d M Y') }}
                        </span>
                    </x-managers.table.cell>

                    {{-- Status --}}
                    <x-managers.table.cell>
                        <x-managers.ui.badge :color="$invoice->status->color()">
                            {{ $invoice->status->label() }}
                        </x-managers.ui.badge>
                    </x-managers.table.cell>

                    <x-managers.table.cell class="text-right">
                        <div class="flex gap-2">
                            {{-- Detail Button --}}
                            <x-managers.ui.button wire:click="detail({{ $invoice->id }})" variant="info"
                                size="sm">
                                <flux:icon.eye class="w-4" />
                            </x-managers.ui.button>

                            {{-- Edit Button --}}
                            <x-managers.ui.button wire:click="edit({{ $invoice->id }})" variant="secondary"
                                size="sm">
                                <flux:icon.pencil class="w-4" />
                            </x-managers.ui.button>

                            {{-- Delete Button --}}
                            {{-- <x-managers.ui.button wire:click="confirmDelete({{ $invoice->id }})" id="delete-invoice"
                                variant="danger" size="sm">
                                <flux:icon.trash class="w-4" />
                            </x-managers.ui.button> --}}
                        </div>
                    </x-managers.table.cell>
                </x-managers.table.row>
            @empty
                <x-managers.table.row>
                    <x-managers.table.cell colspan="7" class="text-center text-gray-500">
                        Tidak ada data tagihan ditemukan.
                    </x-managers.table.cell>
                </x-managers.table.row>
            @endforelse
        </x-managers.table.body>
    </x-managers.table.table>

    {{-- Pagination --}}
    <x-managers.ui.pagination :paginator="$invoices" />
</x-managers.ui.card>

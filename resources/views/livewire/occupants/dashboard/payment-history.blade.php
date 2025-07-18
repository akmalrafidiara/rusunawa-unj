<div class="flex flex-col gap-6">
    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">Riwayat Pembayaran</h2>

    {{-- Toolbar --}}
    <div class="flex flex-col sm:flex-row justify-between gap-4">
        {{-- Search Form --}}
        <x-managers.form.input wire:model.live="search" clearable placeholder="Cari No. Invoice atau Deskripsi..."
            icon="magnifying-glass" class="w-full sm:w-1/2" />

        <div class="flex gap-4">
            {{-- Dropdown for Filters --}}
            <x-managers.ui.dropdown class="flex flex-col gap-2">
                <x-slot name="trigger">
                    <flux:icon.adjustments-horizontal class="w-5 h-5" />
                </x-slot>
                @php
                    $orderByOptions = [
                        ['value' => 'due_at', 'label' => 'Tanggal Jatuh Tempo'],
                        ['value' => 'created_at', 'label' => 'Tanggal Dibuat'],
                        ['value' => 'amount', 'label' => 'Jumlah'],
                    ];

                    $sortOptions = [['value' => 'asc', 'label' => 'Menaik'], ['value' => 'desc', 'label' => 'Menurun']];
                @endphp

                <x-managers.form.small>Filter Status</x-managers.form.small>
                <div class="flex gap-2">
                    <x-managers.ui.dropdown-picker wire:model.live="statusFilter" :options="$statusOptions" label="Semua Status"
                        wire:key="dropdown-status-filter" />
                </div>

                <x-managers.form.small>Urutkan</x-managers.form.small>
                <div class="flex gap-2">
                    <x-managers.ui.dropdown-picker wire:model.live="orderBy" :options="$orderByOptions"
                        label="Urutkan Berdasarkan" wire:key="dropdown-order-by" />

                    <x-managers.ui.dropdown-picker wire:model.live="sort" :options="$sortOptions" label="Sort"
                        wire:key="dropdown-sort" />
                </div>
            </x-managers.ui.dropdown>
        </div>
    </div>

    {{-- Data Table --}}
    <x-managers.ui.card class="p-0">
        <x-managers.table.table :headers="['Nomor Invoice', 'Unit', 'Jumlah', 'Jatuh Tempo', 'Dibayar Pada', 'Status', 'Aksi']">
            <x-managers.table.body>
                @forelse ($invoices as $invoice)
                    <x-managers.table.row wire:key="{{ $invoice->id }}">
                        {{-- Invoice Number --}}
                        <x-managers.table.cell>
                            <span class="font-medium text-gray-900 dark:text-white">
                                {{ $invoice->invoice_number }}
                            </span>
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

                        {{-- Paid At --}}
                        <x-managers.table.cell>
                            <span class="text-gray-600 dark:text-gray-400">
                                {{ $invoice->paid_at ? $invoice->paid_at->translatedFormat('d M Y') : '-' }}
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
                                {{-- Detail Button (Opsional, jika ada modal detail invoice terpisah) --}}
                                <x-managers.ui.button wire:click="viewInvoiceDetails({{ $invoice->id }})"
                                    variant="info" size="sm" title="Lihat Detail Invoice">
                                    <flux:icon name="text" class="w-4" />
                                </x-managers.ui.button>

                                {{-- Download PDF Button (jika ingin mengunduh per invoice) --}}
                                @if ($invoice->status == \App\Enums\InvoiceStatus::PAID)
                                    <a href="{{ route('occupant.invoice.download', $invoice->id) }}" target="_blank"
                                        class="p-2 rounded-md text-red-600 hover:bg-red-100 dark:text-red-400 dark:hover:bg-red-900/30 transition-colors"
                                        title="Unduh PDF Invoice">
                                        <flux:icon name="document-text" class="w-4 h-4" />
                                    </a>
                                @endif
                            </div>
                        </x-managers.table.cell>
                    </x-managers.table.row>
                @empty
                    <x-managers.table.row>
                        <x-managers.table.cell colspan="7" class="text-center text-gray-500">
                            Tidak ada riwayat pembayaran yang ditemukan.
                        </x-managers.table.cell>
                    </x-managers.table.row>
                @endforelse
            </x-managers.table.body>
        </x-managers.table.table>

        {{-- Pagination --}}
        {{-- <x-managers.ui.pagination :paginator="$invoices" /> --}}
    </x-managers.ui.card>
</div>

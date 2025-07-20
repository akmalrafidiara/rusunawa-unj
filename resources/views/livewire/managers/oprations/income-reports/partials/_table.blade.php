<div class="flex flex-col gap-4">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <h3 class="text-xl font-bold">Riwayat Tagihan</h3>
        <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">
            {{-- Per Page Input --}}
            <span class="text-sm">Baris</span>
            <div class="w-20">
                <x-managers.form.input wire:model.live="perPage" type="number" placeholder="15" />
            </div>

            {{-- Export Button --}}
            <span class="text-sm">Unduh</span>
            <x-managers.ui.button-export />
        </div>
    </div>
    <x-managers.ui.card class="p-0">
        <x-managers.table.table :headers="['Invoice', 'Tgl. Bayar', 'Penghuni', 'Jenis', 'Nominal', 'Aksi']">
            <x-managers.table.body>
                @forelse($recentInvoices as $invoice)
                    <x-managers.table.row wire:key="{{ $invoice->id }}">
                        <x-managers.table.cell>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mb-1"
                                title="{{ $invoice->description }}">
                                {{ Str::limit($invoice->description, 30) }}</div>
                            <span
                                class="font-bold text-lg text-gray-900 dark:text-gray-100">{{ $invoice->invoice_number }}</span>
                        </x-managers.table.cell>
                        <x-managers.table.cell>
                            <span class="font-semibold">{{ $invoice->paid_at->translatedFormat('d M Y') }}</span>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ $invoice->paid_at->translatedFormat('H:i') }} WIB</div>
                        </x-managers.table.cell>
                        <x-managers.table.cell>
                            <span
                                class="font-semibold">{{ $invoice->contract->occupants->first()->full_name ?? 'N/A' }}</span>
                            <div class="text-xs text-gray-500 dark:text-gray-400">Kamar:
                                {{ $invoice->contract->unit->room_number ?? 'N/A' }}</div>
                        </x-managers.table.cell>
                        <x-managers.table.cell>
                            @if ($invoice->contract->pricing_basis)
                                @php($basis = \App\Enums\PricingBasis::tryFrom($invoice->contract->pricing_basis->value))
                                <x-managers.ui.badge
                                    :color="$basis?->color() ?? 'secondary'">{{ $basis?->label() ?? '' }}</x-managers.ui.badge>
                            @endif
                        </x-managers.table.cell>
                        <x-managers.table.cell>
                            <span
                                class="font-mono font-bold text-emerald-600 dark:text-emerald-400 text-md whitespace-nowrap">Rp{{ number_format($invoice->amount, 0, ',', '.') }}</span>
                        </x-managers.table.cell>
                        <x-managers.table.cell class="text-right">
                            <x-managers.ui.button wire:click="viewInvoiceDetails({{ $invoice->id }})" variant="info"
                                size="sm" title="Lihat Detail">
                                <flux:icon name="eye" class="w-4 h-4" />
                            </x-managers.ui.button>
                        </x-managers.table.cell>
                    </x-managers.table.row>
                @empty
                    <x-managers.table.row>
                        <x-managers.table.cell colspan="6" class="text-center text-gray-500 py-8">
                            <flux:icon name="inbox" class="w-12 h-12 mx-auto text-gray-400" />
                            <p class="mt-2">Tidak ada data ditemukan untuk filter yang dipilih.</p>
                        </x-managers.table.cell>
                    </x-managers.table.row>
                @endforelse
            </x-managers.table.body>
        </x-managers.table.table>
        <div class="border-t border-gray-200 dark:border-zinc-700">
            <x-managers.ui.pagination :paginator="$recentInvoices" />
        </div>
    </x-managers.ui.card>
</div>

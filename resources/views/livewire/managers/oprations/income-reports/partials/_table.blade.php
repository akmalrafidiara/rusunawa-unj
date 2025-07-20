<!-- Income Report Table -->
<div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700 overflow-hidden">
    <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">Detail Laporan Pendapatan</h3>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                    Menampilkan {{ $recentInvoices->count() }} dari {{ $recentInvoices->total() }} tagihan
                </p>
            </div>
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <span class="text-sm text-zinc-500 dark:text-zinc-400">Baris:</span>
                    <x-managers.ui.dropdown-picker wire:model.live="perPage" :options="[10, 15, 25, 50]" label="15"
                        class="w-20" />
                </div>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full">
            <thead class="bg-zinc-50 dark:bg-zinc-700/50">
                <tr>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                        Tagihan
                    </th>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                        Tanggal Bayar
                    </th>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                        Penghuni
                    </th>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                        Jenis
                    </th>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                        Nominal
                    </th>
                    <th
                        class="px-6 py-3 text-left text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">
                        Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @forelse($recentInvoices as $invoice)
                    <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-zinc-900 dark:text-white">
                                    {{ $invoice->invoice_number }}
                                </span>
                                <span class="text-xs text-zinc-500 dark:text-zinc-400"
                                    title="{{ $invoice->description }}">
                                    {{ Str::limit($invoice->description, 30) }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-zinc-900 dark:text-white">
                                    {{ $invoice->paid_at->translatedFormat('d M Y') }}
                                </span>
                                <span class="text-xs text-zinc-500 dark:text-zinc-400">
                                    {{ $invoice->paid_at->translatedFormat('H:i') }} WIB
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex flex-col">
                                <span class="text-sm font-medium text-zinc-900 dark:text-white">
                                    {{ $invoice->contract->occupants->first()->full_name ?? 'N/A' }}
                                </span>
                                <span class="text-xs text-zinc-500 dark:text-zinc-400">
                                    Kamar: {{ $invoice->contract->unit->room_number ?? 'N/A' }}
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if ($invoice->contract->pricing_basis)
                                @php($basis = \App\Enums\PricingBasis::tryFrom($invoice->contract->pricing_basis->value))
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $basis?->color() === 'primary'
                                        ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400'
                                        : ($basis?->color() === 'success'
                                            ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400'
                                            : 'bg-zinc-100 text-zinc-800 dark:bg-zinc-900/20 dark:text-zinc-400') }}">
                                    {{ $basis?->label() ?? '' }}
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 font-mono">
                                Rp {{ number_format($invoice->amount, 0, ',', '.') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right">
                            <x-managers.ui.button wire:click="viewInvoiceDetails({{ $invoice->id }})" variant="info"
                                size="sm" title="Lihat Detail">
                                <flux:icon.eye class="w-4 h-4" />
                            </x-managers.ui.button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <flux:icon.document-text class="w-12 h-12 text-zinc-400 dark:text-zinc-600 mb-4" />
                                <h3 class="text-lg font-medium text-zinc-900 dark:text-white mb-2">Tidak ada data</h3>
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">
                                    Tidak ada tagihan yang ditemukan untuk periode yang dipilih.
                                </p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($recentInvoices->hasPages())
        <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700">
            {{ $recentInvoices->links() }}
        </div>
    @endif
</div>

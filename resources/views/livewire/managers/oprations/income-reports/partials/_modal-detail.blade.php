<!-- Invoice Detail Modal -->
@if ($showModal && $selectedInvoice)
    <div class="fixed inset-0 z-50 overflow-y-auto" x-data="{ show: @entangle('showModal') }" x-show="show" x-transition.opacity>
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 transition-opacity bg-black opacity-75 z-10" wire:click="closeModal"></div>

            <!-- Modal content -->
            <div
                class="relative inline-block w-full max-w-2xl my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-zinc-800 shadow-xl rounded-xl z-20">
                <!-- Modal Header -->
                <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 dark:border-zinc-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Detail Tagihan
                    </h3>
                    <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                        <flux:icon.x-mark class="w-6 h-6" />
                    </button>
                </div>

                <!-- Modal Body -->
                <div class="px-6 py-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Invoice Information -->
                        <div class="space-y-4">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wide">
                                Informasi Tagihan
                            </h4>

                            <div class="space-y-3">
                                <div>
                                    <label
                                        class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                        Nomor Invoice
                                    </label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white font-mono">
                                        {{ $selectedInvoice->invoice_number }}
                                    </p>
                                </div>

                                <div>
                                    <label
                                        class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                        Deskripsi
                                    </label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                        {{ $selectedInvoice->description }}
                                    </p>
                                </div>

                                <div>
                                    <label
                                        class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                        Jumlah
                                    </label>
                                    <p class="mt-1 text-lg font-semibold text-green-600 dark:text-green-400">
                                        Rp {{ number_format($selectedInvoice->amount, 0, ',', '.') }}
                                    </p>
                                </div>

                                <div>
                                    <label
                                        class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                        Status
                                    </label>
                                    <div class="mt-1">
                                        @php
                                            $statusClass = match ($selectedInvoice->status->value) {
                                                'paid'
                                                    => 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-400',
                                                'pending'
                                                    => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-400',
                                                'overdue'
                                                    => 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-400',
                                                default
                                                    => 'bg-gray-100 text-gray-800 dark:bg-zinc-700/30 dark:text-gray-400',
                                            };
                                        @endphp
                                        <span
                                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusClass }}">
                                            {{ $selectedInvoice->status->label() }}
                                        </span>
                                    </div>
                                </div>

                                <div>
                                    <label
                                        class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                        Tanggal Jatuh Tempo
                                    </label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                        {{ $selectedInvoice->due_at ? \Carbon\Carbon::parse($selectedInvoice->due_at)->translatedFormat('d F Y') : '-' }}
                                    </p>
                                </div>

                                <div>
                                    <label
                                        class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                        Tanggal Pembayaran
                                    </label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                        {{ $selectedInvoice->paid_at ? \Carbon\Carbon::parse($selectedInvoice->paid_at)->translatedFormat('d F Y H:i') : '-' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Contract and Occupant Information -->
                        <div class="space-y-4">
                            <h4 class="text-sm font-semibold text-gray-900 dark:text-white uppercase tracking-wide">
                                Informasi Kontrak & Penghuni
                            </h4>

                            <div class="space-y-3">
                                <div>
                                    <label
                                        class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                        Kode Kontrak
                                    </label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white font-mono">
                                        {{ $selectedInvoice->contract->contract_code ?? '-' }}
                                    </p>
                                </div>

                                <div>
                                    <label
                                        class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                        Nama Penghuni
                                    </label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                        {{ $selectedInvoice->contract->occupants->pluck('full_name')->join(', ') ?: '-' }}
                                    </p>
                                </div>

                                <div>
                                    <label
                                        class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                        Nomor Unit
                                    </label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                        {{ $selectedInvoice->contract->unit->unit_number ?? '-' }}
                                    </p>
                                </div>

                                @if ($selectedInvoice->contract->unit->room_number)
                                    <div>
                                        <label
                                            class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                            Nomor Kamar
                                        </label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                            {{ $selectedInvoice->contract->unit->room_number }}
                                        </p>
                                    </div>
                                @endif

                                @if ($selectedInvoice->contract->occupantType)
                                    <div>
                                        <label
                                            class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                            Tipe Penghuni
                                        </label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                            {{ $selectedInvoice->contract->occupantType->name ?? '-' }}
                                        </p>
                                    </div>
                                @endif

                                <div>
                                    <label
                                        class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                        Dibuat Pada
                                    </label>
                                    <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                        {{ $selectedInvoice->created_at->translatedFormat('d F Y H:i') }}
                                    </p>
                                </div>

                                @if ($selectedInvoice->updated_at != $selectedInvoice->created_at)
                                    <div>
                                        <label
                                            class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                            Terakhir Diupdate
                                        </label>
                                        <p class="mt-1 text-sm text-gray-900 dark:text-white">
                                            {{ $selectedInvoice->updated_at->translatedFormat('d F Y H:i') }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div
                    class="flex items-center justify-end px-6 py-4 border-t border-gray-200 dark:border-zinc-700 space-x-3">
                    <x-managers.ui.button wire:click="closeModal" variant="secondary">
                        Tutup
                    </x-managers.ui.button>
                </div>
            </div>
        </div>
    </div>
@endif

@if ($invoiceIdBeingSelected)
    <x-managers.ui.modal title="Detail Data Tagihan" :show="$showModal && $modalType === 'detail'" class="max-w-3xl">
        <div class="space-y-6">
            {{-- Header Tagihan --}}
            <div
                class="flex items-center gap-4 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-lg border border-blue-100 dark:border-blue-800">
                <flux:icon.banknotes class="h-12 w-12 text-blue-600 dark:text-blue-400" />
                <div>
                    <h3 class="text-xl font-bold text-zinc-800 dark:text-zinc-100">
                        Tagihan #{{ $invoiceNumber }}
                    </h3>
                    <p class="text-sm text-zinc-600 dark:text-zinc-300">
                        Detail informasi tagihan dan status pembayaran
                    </p>
                </div>
            </div>

            {{-- Informasi Tagihan & Status --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Kartu Informasi Tagihan --}}
                <div
                    class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-4 shadow-sm">
                    <h4 class="text-lg font-semibold text-zinc-800 dark:text-zinc-100 mb-4 flex items-center gap-2">
                        <flux:icon name="receipt-percent" class="h-5 w-5 text-purple-500" />
                        Detail Tagihan
                    </h4>
                    <div class="space-y-3 text-sm">
                        <div class="flex justify-between py-2 border-b border-zinc-100 dark:border-zinc-700">
                            <span class="text-zinc-600 dark:text-zinc-300">Nomor Invoice</span>
                            <span class="font-semibold text-zinc-800 dark:text-zinc-100">{{ $invoiceNumber }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-zinc-100 dark:border-zinc-700">
                            <span class="text-zinc-600 dark:text-zinc-300">Jumlah</span>
                            <span class="font-semibold text-zinc-800 dark:text-zinc-100">
                                Rp {{ number_format($amount, 0, ',', '.') }}
                            </span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-zinc-100 dark:border-zinc-700">
                            <span class="text-zinc-600 dark:text-zinc-300">Tanggal Jatuh Tempo</span>
                            <span class="font-semibold text-zinc-800 dark:text-zinc-100">
                                {{ \Carbon\Carbon::parse($dueAt)->translatedFormat('d F Y') }}
                            </span>
                        </div>
                        <div class="flex justify-between py-2">
                            <span class="text-zinc-600 dark:text-zinc-300">Tanggal Pembayaran</span>
                            <span class="font-semibold text-zinc-800 dark:text-zinc-100">
                                {{ $paidAt ? \Carbon\Carbon::parse($paidAt)->translatedFormat('d F Y') : '-' }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Kartu Status & Deskripsi --}}
                <div
                    class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-4 shadow-sm">
                    <h4 class="text-lg font-semibold text-zinc-800 dark:text-zinc-100 mb-4 flex items-center gap-2">
                        <flux:icon name="shield-check" class="h-5 w-5 text-green-500" />
                        Status & Deskripsi
                    </h4>
                    <div class="space-y-3 text-sm">
                        <div
                            class="flex justify-between items-center py-2 border-b border-zinc-100 dark:border-zinc-700">
                            <span class="text-zinc-600 dark:text-zinc-300">Status Pembayaran</span>
                            @php
                                $statusEnum = \App\Enums\InvoiceStatus::tryFrom($status);
                            @endphp
                            <x-managers.ui.badge :color="$statusEnum?->color()">
                                {{ $statusEnum?->label() }}
                            </x-managers.ui.badge>
                        </div>
                        <div class="py-2">
                            <span class="text-zinc-600 dark:text-zinc-300 block mb-1">Deskripsi</span>
                            <p class="font-semibold text-zinc-800 dark:text-zinc-100 text-sm leading-relaxed">
                                {{ $description }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bagian Informasi Kontrak & Penghuni --}}
            @if ($contractId)
                @php
                    $contract = \App\Models\Contract::with(['occupants', 'unit.unitCluster'])->find($contractId);
                @endphp
                @if ($contract)
                    <div
                        class="bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-lg p-4 shadow-sm">
                        <h4 class="text-lg font-semibold text-zinc-800 dark:text-zinc-100 mb-4 flex items-center gap-2">
                            <flux:icon name="document-text" class="h-5 w-5 text-orange-500" />
                            Detail Kontrak & Penghuni
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-3 text-sm">
                            <div class="flex justify-between py-2 border-b border-zinc-100 dark:border-zinc-700">
                                <span class="text-zinc-600 dark:text-zinc-300">Kode Kontrak</span>
                                <span
                                    class="font-semibold text-zinc-800 dark:text-zinc-100 text-right">{{ $contract->contract_code }}</span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-zinc-100 dark:border-zinc-700">
                                <span class="text-zinc-600 dark:text-zinc-300">Unit</span>
                                <span class="font-semibold text-zinc-800 dark:text-zinc-100 text-right">
                                    {{ $contract->unit->unitCluster->name ?? 'N/A' }} |
                                    {{ $contract->unit->room_number ?? 'N/A' }}
                                </span>
                            </div>
                            <div class="flex justify-between py-2">
                                <span class="text-zinc-600 dark:text-zinc-300">Nama Penghuni</span>
                                <span class="font-semibold text-zinc-800 dark:text-zinc-100 text-right">
                                    @if ($contract->occupants->isNotEmpty())
                                        {{ $contract->occupants->first()->full_name }}
                                    @else
                                        -
                                    @endif
                                </span>
                            </div>
                            <div class="flex justify-between py-2">
                                <span class="text-zinc-600 dark:text-zinc-300">Email Penghuni</span>
                                <span class="font-semibold text-zinc-800 dark:text-zinc-100 text-right">
                                    @if ($contract->occupants->isNotEmpty())
                                        {{ $contract->occupants->first()->email }}
                                    @else
                                        -
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                @endif
            @endif
        </div>

        {{-- Tombol Tutup --}}
        <div class="flex justify-end mt-6">
            <x-managers.ui.button type="button" variant="secondary" wire:click="$set('showModal', false)">
                Tutup
            </x-managers.ui.button>
        </div>
    </x-managers.ui.modal>
@endif

<div class="bg-white dark:bg-zinc-800 rounded-lg shadow-md border dark:border-zinc-700 p-6">
    <h3 class="text-xl font-bold mb-6 text-gray-900 dark:text-gray-100">Detail Pembayaran</h3>

    @if ($contract && $latestInvoice)
        <div class="space-y-4 text-gray-700 dark:text-gray-300">
            {{-- Kamar --}}
            <div class="flex items-center justify-between border-b border-gray-200 dark:border-zinc-700 pb-3">
                <div class="flex items-center gap-2">
                    <flux:icon.home class="w-5 h-5 text-indigo-500" />
                    <span class="font-semibold">Kamar:</span>
                </div>
                <span
                    class="font-bold text-lg text-gray-900 dark:text-gray-100">{{ $contract->unit->room_number }}</span>
            </div>

            {{-- PIC Kamar --}}
            <div class="flex items-center justify-between border-b border-gray-200 dark:border-zinc-700 pb-3">
                <div class="flex items-center gap-2">
                    <flux:icon.user class="w-5 h-5 text-indigo-500" />
                    <span class="font-semibold">PIC Kamar:</span>
                </div>
                <span>{{ $contract->pic->first()->full_name }}</span>
            </div>

            {{-- Nomor Virtual Account --}}
            <div class="flex items-center justify-between border-b border-gray-200 dark:border-zinc-700 pb-3">
                <div class="flex items-center gap-2">
                    <flux:icon.credit-card class="w-5 h-5 text-indigo-500" />
                    <span class="font-semibold">Nomor Virtual Account:</span>
                </div>
                <span
                    class="font-mono bg-gray-100 dark:bg-zinc-700 text-gray-900 dark:text-gray-100 p-1 rounded-md text-sm">
                    {{ $contract->unit->virtual_account_number }}
                </span>
            </div>

            {{-- Durasi Sewa --}}
            <div class="flex items-center justify-between border-b border-gray-200 dark:border-zinc-700 pb-3">
                <div class="flex items-center gap-2">
                    <flux:icon name="calendar" class="w-5 h-5 text-indigo-500" />
                    <span class="font-semibold">Durasi Sewa:</span>
                </div>
                <span>
                    {{ $contract->start_date->translatedFormat('d M Y') }} -
                    {{ $contract->end_date->translatedFormat('d M Y') }}
                </span>
            </div>

            {{-- Tanggal Jatuh Tempo --}}
            <div class="flex items-center justify-between border-b border-gray-200 dark:border-zinc-700 pb-3">
                <div class="flex items-center gap-2">
                    <flux:icon name="calendar-days" class="w-5 h-5 text-red-500" />
                    <span class="font-semibold">Tanggal Jatuh Tempo:</span>
                </div>
                <span class="text-red-500 font-medium">
                    {{ $latestInvoice->due_at->translatedFormat('d F Y H:i') }} WIB
                </span>
            </div>

            {{-- Status Tagihan --}}
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <flux:icon.tag class="w-5 h-5 text-blue-500" />
                    <span class="font-semibold">Status Tagihan:</span>
                </div>
                <x-managers.ui.badge :color="$latestInvoice->status->color()">
                    {{ $latestInvoice->status->label() }}
                </x-managers.ui.badge>
            </div>

            {{-- Subtotal & Tombol Aksi --}}
            <div class="flex justify-between items-center border-t border-gray-200 dark:border-zinc-600 pt-4 mt-4">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Subtotal</p>
                    <p class="text-3xl font-bold text-emerald-600 dark:text-emerald-400">
                        Rp{{ number_format($latestInvoice->amount, 0, ',', '.') }}
                    </p>
                </div>

                {{-- Tombol "Bayar" hanya jika status UNPAID --}}
                @if ($latestInvoice->status == \App\Enums\InvoiceStatus::UNPAID)
                    <x-managers.ui.button
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg transition-colors shadow-md"
                        wire:click="openPaymentModal" {{-- Asumsi ada metode ini di Livewire component --}}>
                        <flux:icon name="banknote" class="w-5 h-5 mr-2" /> Bayar Sekarang
                    </x-managers.ui.button>
                @endif
            </div>

            {{-- Notifikasi Status Pembayaran --}}
            @if ($latestInvoice->status == \App\Enums\InvoiceStatus::UNPAID)
                {{-- Livewire component untuk upload bukti pembayaran --}}
                <div class="mt-6">
                    <livewire:occupants.dashboard.upload-payment-proof :invoice="$latestInvoice" />
                </div>
            @elseif ($latestInvoice->status == \App\Enums\InvoiceStatus::PENDING_PAYMENT_VERIFICATION)
                {{-- Pesan jika sedang diverifikasi --}}
                <div
                    class="mt-4 p-4 rounded-md bg-orange-50 dark:bg-orange-900/20 text-orange-700 dark:text-orange-300 flex items-center gap-3">
                    <flux:icon.exclamation-circle class="w-6 h-6 flex-shrink-0" />
                    <p>Bukti pembayaran untuk invoice ini sedang dalam proses verifikasi. Mohon tunggu konfirmasi dari
                        manajer.</p>
                </div>
            @endif
        </div>

        {{-- Tombol "Lihat Riwayat Pembayaran" --}}
        {{-- Tombol ini dipindahkan ke luar kondisi if/elseif agar konsisten muncul di bawah --}}
        <div class="mt-6">
            <x-managers.ui.button
                class="w-full bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-3 px-4 rounded-lg transition-colors shadow-md">
                <flux:icon name="folder" class="w-5 h-5 mr-2" /> Lihat Riwayat Pembayaran
            </x-managers.ui.button>
        </div>
    @else
        <p class="text-gray-500 dark:text-gray-400 py-4 text-center">Informasi pembayaran tidak tersedia untuk saat ini.
        </p>
    @endif
</div>

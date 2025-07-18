<div class="bg-white dark:bg-zinc-800 rounded-lg shadow-md border dark:border-zinc-700 p-6">
    <h3 class="text-xl font-bold mb-4">Detail Pembayaran</h3>
    @if ($contract && $latestInvoice)
        <div class="space-y-3">
            <div class="flex justify-between">
                <span class="font-semibold">Kamar:</span>
                <span class="font-bold text-lg">{{ $contract->unit->room_number }}</span>
            </div>
            <div class="flex justify-between">
                <span class="font-semibold">PIC Kamar:</span>
                <span>{{ $contract->pic->first()->full_name }}</span>
            </div>
            <div class="flex justify-between">
                <span class="font-semibold">Nomor Virtual Account:</span>
                <span
                    class="font-mono bg-gray-100 dark:bg-zinc-700 p-1 rounded">{{ $contract->unit->virtual_account_number }}</span>
            </div>
            <div class="flex justify-between">
                <span class="font-semibold">Durasi Sewa:</span>
                <span>{{ $contract->start_date->translatedFormat('d M Y') }} -
                    {{ $contract->end_date->translatedFormat('d M Y') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="font-semibold">Tanggal Jatuh Tempo:</span>
                <span class="text-red-500 font-medium">{{ $latestInvoice->due_at->translatedFormat('d M Y H:i') }}
                    WIB</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="font-semibold">Status Tagihan:</span>
                <x-managers.ui.badge :color="$latestInvoice->status->color()">
                    {{ $latestInvoice->status->label() }}
                </x-managers.ui.badge>
            </div>
            <div class="flex justify-between items-center border-t dark:border-zinc-600 pt-3 mt-3 ">
                <div>
                    <p class="text-sm">Subtotal</p>
                    <p class="text-2xl font-bold">Rp{{ number_format($latestInvoice->amount, 0, ',', '.') }}</p>
                </div>
                <button>
                    <x-managers.ui.button
                        class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                        Bayar
                    </x-managers.ui.button>
                </button>
            </div>
            @if ($latestInvoice->status == \App\Enums\InvoiceStatus::UNPAID)
                {{-- Asumsi ada Enum InvoiceStatus::Pending --}}
                <div class="mt-6">
                    <livewire:occupants.dashboard.upload-payment-proof :invoice="$latestInvoice" />
                </div>
            @elseif ($latestInvoice->status == \App\Enums\InvoiceStatus::PENDING_PAYMENT_VERIFICATION)
                {{-- Jika ada status khusus setelah upload --}}
                <p class="mt-4 text-center text-orange-500 dark:text-orange-400">Bukti pembayaran untuk invoice ini
                    sedang dalam proses verifikasi.</p>
            @else
                <button
                    class="mt-4 w-full bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-2 px-4 rounded-lg transition-colors">
                    Lihat Riwayat Pembayaran
                </button>
            @endif
        </div>
        <button
            class="mt-4 w-full bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-2 px-4 rounded-lg transition-colors">
            Lihat Riwayat Pembayaran
        </button>
    @else
        <p class="text-gray-500 dark:text-gray-400">Informasi pembayaran tidak tersedia.</p>
    @endif
</div>

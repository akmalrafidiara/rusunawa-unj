<div class="bg-white dark:bg-zinc-800 rounded-lg shadow-md border dark:border-zinc-700 p-6">
    <h3 class="text-xl font-bold mb-6 text-gray-900 dark:text-gray-100">Status</h3>

    @if (
        ($contract && $latestInvoice && $latestInvoice->status == \App\Enums\InvoiceStatus::UNPAID) ||
            ($latestInvoice && $latestInvoice->status == \App\Enums\InvoiceStatus::PENDING_PAYMENT_VERIFICATION))
        <div class="space-y-4 text-gray-700 dark:text-gray-300">

            {{-- Nomor Virtual Account --}}
            <div class="flex items-center justify-between border-b border-gray-200 dark:border-zinc-700 pb-3">
                <div class="flex items-center gap-2">
                    <flux:icon name="credit-card" class="w-5 h-5 text-indigo-500" />
                    <span class="font-semibold">Virtual Account | Mandiri:</span>
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
            <div class="flex justify-between border-t border-gray-200 dark:border-zinc-600 pt-4 mt-4">
                <div>
                    <p class="text-sm text-gray-600 dark:text-gray-400">Subtotal</p>
                    <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">
                        Rp{{ number_format($latestInvoice->amount, 0, ',', '.') }}
                    </p>
                </div>

                {{-- Tombol "Bayar" hanya jika status UNPAID --}}
                @if ($latestInvoice->status == \App\Enums\InvoiceStatus::UNPAID)
                    <x-managers.ui.button
                        class="bg-emerald-600 hover:bg-emerald-700 text-sm text-white font-bold rounded-lg transition-colors shadow-md"
                        wire:click="showPaymentForm">
                        Konfirmasi Pembayaran
                    </x-managers.ui.button>
                @endif
            </div>
            @if (
                $latestInvoice->status == \App\Enums\InvoiceStatus::UNPAID &&
                    $latestInvoice->payments->last()->status == \App\Enums\PaymentStatus::REJECTED->value)
                <div
                    class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-6 text-center shadow-sm">
                    <div class="flex flex-col items-center justify-center space-y-4">
                        <flux:icon name="x-circle" class="w-12 h-12 text-red-600 dark:text-red-400" />
                        <h4 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Pembayaran Ditolak
                        </h4>
                        <p class="text-gray-700 dark:text-gray-300">
                            {{ $latestInvoice->payments->last()->verificationLogs->last()->reason }}
                        </p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                            Anda dapat mengajukan konfirmasi pembayaran ulang.
                        </p>
                    </div>
                </div>
            @endif

            {{-- Notifikasi Status Pembayaran --}}
            @if ($latestInvoice->status == \App\Enums\InvoiceStatus::PENDING_PAYMENT_VERIFICATION)
                {{-- Pesan jika sedang diverifikasi --}}
                <div
                    class="mt-4 p-4 rounded-md bg-orange-100 dark:bg-orange-900/20 text-orange-700 dark:text-orange-300 flex items-center gap-3">
                    <flux:icon.exclamation-circle class="w-6 h-6 flex-shrink-0" />
                    <p>Bukti pembayaran untuk invoice ini sedang dalam proses verifikasi. Mohon tunggu konfirmasi
                        dari
                        pengelola.</p>
                </div>
            @endif
        </div>
    @else
        @if (isset($occupant) && $occupant->status === \App\Enums\OccupantStatus::PENDING_VERIFICATION && !isset($invoices))
            {{-- Pesan jika occupant sedang dalam proses verifikasi --}}
            <div
                class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6 text-center shadow-sm">
                <div class="flex flex-col items-center justify-center space-y-4">
                    <flux:icon name="information-circle" class="w-12 h-12 text-blue-600 dark:text-blue-400" />
                    <h4 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Verifikasi Data Sedang Diproses
                    </h4>
                    <p class="text-gray-700 dark:text-gray-300">
                        Informasi pembayaran belum tersedia karena data Anda sedang dalam proses verifikasi oleh admin.
                        Mohon tunggu konfirmasi.
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                        Anda akan mendapatkan notifikasi setelah proses verifikasi selesai.
                    </p>
                </div>
            </div>
        @elseif(
            $contract &&
                $contract->occupants->contains(function ($occupant) {
                    return $occupant->status === \App\Enums\OccupantStatus::PENDING_VERIFICATION;
                }))
            {{-- Pesan jika occupant sedang dalam proses verifikasi --}}
            <div
                class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6 text-center shadow-sm">
                <div class="flex flex-col items-center justify-center space-y-4">
                    <flux:icon name="information-circle" class="w-12 h-12 text-blue-600 dark:text-blue-400" />
                    <h4 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Verifikasi Data Sedang Diproses
                    </h4>
                    <p class="text-gray-700 dark:text-gray-300">
                        Data
                        {{ $contract->occupants->where('status', \App\Enums\OccupantStatus::PENDING_VERIFICATION)->pluck('full_name')->join(', ') }}
                        sedang dalam proses verifikasi oleh admin.
                        Mohon tunggu konfirmasi.
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                        Notifikasi akan didapatkan setelah proses verifikasi selesai.
                    </p>
                </div>
            </div>
        @elseif (isset($occupant) && $occupant->status === \App\Enums\OccupantStatus::REJECTED)
            <div
                class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-6 text-center shadow-sm">
                <div class="flex flex-col items-center justify-center space-y-4">
                    <flux:icon name="x-circle" class="w-12 h-12 text-red-600 dark:text-red-400" />
                    <h4 class="text-xl font-semibold text-gray-900  dark:text-gray-100">Verifikasi Data Ditolak
                    </h4>
                    <p class="text-gray-700 dark:text-gray-300">
                        {{ $occupant->verificationLogs->last()->reason }}
                    </p>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
                        Anda dapat mengajukan permohonan ulang setelah memperbaiki data yang diperlukan.
                    </p>
                    <x-managers.ui.button wire:click="showOccupantForm({{ $occupant->id }})"
                        class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded-lg transition-colors shadow-md mt-4">
                        <flux:icon name="pencil" class="w-5 h-5 mr-2" /> Edit Data
                    </x-managers.ui.button>
                </div>
            </div>
        @else
            {{-- Kondisi jika tidak ada kontrak/invoice atau status bukan pending verification --}}
            <div
                class="bg-gray-50 dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-lg p-6 text-center shadow-sm">
                <div class="flex flex-col items-center justify-center space-y-4">
                    <flux:icon name="check-circle" class="w-12 h-12 text-green-400 dark:text-green-500" />
                    <h4 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Tidak ada informasi terbaru</h4>
                    <p class="text-gray-700 dark:text-gray-300 text-xs">
                        Untuk saat ini, tidak ada informasi yang dapat ditampilkan.
                        Silakan hubungi admin jika Anda merasa ini adalah kesalahan.
                    </p>
                </div>
            </div>
        @endif
    @endif
</div>

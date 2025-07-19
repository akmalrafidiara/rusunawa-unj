{{-- resources/views/livewire/occupants/dashboard/contract-partials/_status-payment-rejected-alert.blade.php --}}

{{-- Alert box for rejected payments --}}
<div
    class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-6 text-center shadow-sm mt-4">
    <div class="flex flex-col items-center justify-center space-y-4">
        <flux:icon name="x-circle" class="w-12 h-12 text-red-600 dark:text-red-400" />
        <h4 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Pembayaran Ditolak</h4>
        {{-- Displays the reason for rejection from the latest payment's verification log --}}
        <p class="text-gray-700 dark:text-gray-300">
            {{ $latestInvoice->payments->last()->verificationLogs->last()->reason ?? 'Alasan tidak tersedia.' }}
        </p>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
            Anda dapat mengajukan konfirmasi pembayaran ulang.
        </p>
    </div>
</div>

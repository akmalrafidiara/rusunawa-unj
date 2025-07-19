{{-- resources/views/livewire/occupants/dashboard/contract-partials/_status-occupant-pending-alert.blade.php --}}

{{-- Message when the current occupant's data verification is pending (e.g., initial application) --}}
<div
    class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6 text-center shadow-sm">
    <div class="flex flex-col items-center justify-center space-y-4">
        <flux:icon name="information-circle" class="w-12 h-12 text-blue-600 dark:text-blue-400" />
        <h4 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Verifikasi Data Sedang Diproses</h4>
        <p class="text-gray-700 dark:text-gray-300">
            Informasi pembayaran belum tersedia karena data Anda sedang dalam proses verifikasi oleh admin.
            Mohon tunggu konfirmasi.
        </p>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
            Anda akan mendapatkan notifikasi setelah proses verifikasi selesai.
        </p>
    </div>
</div>

@php
    $pendingOccupants = $contract->occupants->where('status', \App\Enums\OccupantStatus::PENDING_VERIFICATION);
    $isLoggedInUserPending = $pendingOccupants->contains('id', $occupant->id);
    $otherPendingOccupants = $pendingOccupants->reject(fn($o) => $o->id === $this->occupant->id);
@endphp

<div
    class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6 text-center shadow-sm">
    <div class="flex flex-col items-center justify-center space-y-4">
        <flux:icon name="information-circle" class="w-12 h-12 text-blue-600 dark:text-blue-400" />
        <h4 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Verifikasi Data Sedang Diproses</h4>
        @if ($isLoggedInUserPending)
            <p class="text-gray-700 dark:text-gray-300">
                Informasi pembayaran belum tersedia karena data Anda sedang dalam proses verifikasi oleh admin.
                Mohon tunggu konfirmasi.
            </p>
        @endif
        @if ($otherPendingOccupants->isNotEmpty())
            <p class="text-gray-700 dark:text-gray-300">
                Data penghuni lain dalam kontrak ini ({{ $otherPendingOccupants->pluck('full_name')->join(', ') }})
                sedang dalam proses verifikasi oleh admin.
            </p>
        @endif
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
            Notifikasi akan didapatkan setelah proses verifikasi selesai.
        </p>
    </div>
</div>

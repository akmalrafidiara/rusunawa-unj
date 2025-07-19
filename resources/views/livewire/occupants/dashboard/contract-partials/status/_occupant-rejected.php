{{-- resources/views/livewire/occupants/dashboard/contract-partials/_status-occupant-rejected-alert.blade.php --}}

{{-- Alert box when the occupant's data verification has been rejected --}}
<div
  class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-6 text-center shadow-sm">
  <div class="flex flex-col items-center justify-center space-y-4">
    <flux:icon name="x-circle" class="w-12 h-12 text-red-600 dark:text-red-400" />
    <h4 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Verifikasi Data Ditolak</h4>
    {{-- Displays the reason for rejection from the latest verification log for this occupant --}}
    <p class="text-gray-700 dark:text-gray-300">
      {{ $occupant->verificationLogs->last()->reason ?? 'Alasan tidak tersedia.' }}
    </p>
    <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">
      Anda dapat mengajukan permohonan ulang setelah memperbaiki data yang diperlukan.
    </p>
    {{-- Button to allow the occupant to edit their data --}}
    <x-managers.ui.button wire:click="showOccupantForm({{ $occupant->id }})"
      class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-6 rounded-lg transition-colors shadow-md mt-4">
      <flux:icon name="pencil" class="w-5 h-5 mr-2" /> Edit Data
    </x-managers.ui.button>
  </div>
</div>

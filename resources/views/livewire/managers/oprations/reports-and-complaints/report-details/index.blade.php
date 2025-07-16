<div class="lg:col-span-2 flex flex-col gap-6">
    @if ($reportIdBeingViewed)
        {{-- Card Utama untuk Detail Laporan, Deskripsi, dan Modal --}}
        <x-managers.ui.card class="p-4">
            <div class="flex justify-between items-center mb-4">
                <h4 class="text-lg font-bold text-gray-800 dark:text-white">Detail Laporan #{{ $reportUniqueId }}</h4>
                <div class="flex gap-2">
                    @if ($this->canEditReport())
                    <x-managers.ui.button
                        wire:click="updateStatusForm"
                        variant="primary"
                        size="sm"
                        class="{{ $this->shouldDisableUpdateButton() ? 'bg-gray-400 cursor-not-allowed' : '' }}"
                        wire:attr.disabled="$this->shouldDisableUpdateButton()"
                        title="{{ $this->shouldDisableUpdateButton() ? 'Tidak dapat memperbarui status saat ini.' : '' }}">
                        Perbarui Status
                    </x-managers.ui.button>
                    @endif
                </div>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">Informasi lengkap tentang laporan ini.</p>
            
            @include('livewire.managers.oprations.reports-and-complaints.report-details.partials._report-info')

            @include('livewire.managers.oprations.reports-and-complaints.report-details.partials._report-description')

            @include('livewire.managers.oprations.reports-and-complaints.report-details.partials._modal-update-status')
        </x-managers.ui.card>

        @include('livewire.managers.oprations.reports-and-complaints.report-details.partials._report-logs')
    @else
        <x-managers.ui.card class="p-4 lg:col-span-2 text-center text-gray-500 dark:text-gray-400">
            <p>Pilih laporan dari daftar di samping untuk melihat detail dan riwayat penanganan.</p>
        </x-managers.ui.card>
    @endif
</div>
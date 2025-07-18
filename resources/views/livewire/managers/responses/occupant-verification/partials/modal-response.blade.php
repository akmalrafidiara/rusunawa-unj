<x-managers.ui.modal title="Verifikasi Penghuni" :show="$showModal" class="max-w-2xl">
    <div class="space-y-6">
        @if ($modalType === 'accept')
            <h2 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-4">
                Data Penghuni Diterima
            </h2>
            <x-managers.form.label>Pesan</x-managers.form.label>
            <x-managers.form.input wire:model="responseMessage" />

            <x-managers.form.label>Perbarui Harga (opsional)</x-managers.form.label>
            <x-managers.form.input wire:model="contractPrice" rupiah />
        @elseif ($modalType === 'reject')
            <h2 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-4">
                Data Penghuni Ditolak
            </h2>
            <x-managers.form.textarea wire:model="responseMessage" />
        @endif
        @if ($occupant)
            <div class="mt-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Detail Penghuni</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $occupant->full_name }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">{{ $occupant->email }}</p>
                    </div>
                </div>
            </div>
        @endif
        <div class="flex gap-2 justify-end mt-6">
            <x-managers.ui.button type="button" variant="danger" wire:click="$set('showModal', false)">
                Tutup
            </x-managers.ui.button>
            <x-managers.ui.button type="button" variant="primary"
                wire:click="{{ $modalType === 'accept' ? 'acceptOccupant' : 'rejectOccupant' }}">
                Simpan
            </x-managers.ui.button>
        </div>
</x-managers.ui.modal>

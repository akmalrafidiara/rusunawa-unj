<x-managers.ui.modal title="Verifikasi Pembayaran" :show="$showModal" class="max-w-2xl">
    <div class="space-y-6">
        @if ($modalType === 'accept')
            <h2 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-4">
                Verifikasi Pembayaran Diterima
            </h2>
            <x-managers.form.label>Pesan Verifikasi</x-managers.form.label>
            <x-managers.form.textarea wire:model="responseMessage"
                placeholder="Misalnya: Pembayaran Anda telah kami terima dan invoice telah dilunasi." />
        @elseif ($modalType === 'reject')
            <h2 class="text-md font-semibold text-gray-900 dark:text-gray-100 mb-4">
                Verifikasi Pembayaran Ditolak
            </h2>
            <x-managers.form.textarea wire:model="responseMessage"
                placeholder="Misalnya: Bukti pembayaran tidak jelas. Mohon unggah ulang." />
        @endif
        @if ($payment)
            <div class="mt-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Detail Pembayaran</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nama Penghuni</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ $payment->invoice->contract->occupants->first()->full_name ?? '-' }}</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nomor Invoice</label>
                        <p class="mt-1 text-sm text-gray-900 dark:text-gray-100">
                            {{ $payment->invoice->invoice_number ?? '-' }}</p>
                    </div>
                </div>
            </div>
        @endif
        <div class="flex gap-2 justify-end mt-6">
            <x-managers.ui.button type="button" variant="secondary" wire:click="$set('showModal', false)">
                Tutup
            </x-managers.ui.button>
            <x-managers.ui.button type="button" variant="{{ $modalType === 'accept' ? 'primary' : 'danger' }}"
                wire:click="{{ $modalType === 'accept' ? 'acceptPayment' : 'rejectPayment' }}">
                {{ $modalType === 'accept' ? 'Setujui' : 'Tolak' }}
            </x-managers.ui.button>
        </div>
    </div>
</x-managers.ui.modal>

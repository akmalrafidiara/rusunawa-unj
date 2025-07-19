<x-managers.ui.modal title="Unggah Bukti Pembayaran #{{ $latestInvoice->invoice_number }}" :show="$showModal && $modalType === 'payment'"
    class="max-w-xl">
    <form wire:submit.prevent="savePayment">
        <div class="mb-4">
            <x-frontend.form.file wire:model="proofOfPayment" name="proofOfPayment"
                label="Pilih Bukti Pembayaran (Gambar)" />
        </div>

        <div class="mb-6">
            <x-managers.form.label>
                Catatan Tambahan (Opsional)
            </x-managers.form.label>
            <x-managers.form.input type="text" wire:model="notes" name="notes"
                placeholder="Masukkan catatan tambahan jika diperlukan" />
        </div>

        <div class="flex justify-end mt-4 gap-2">
            <x-managers.ui.button type="button" variant="secondary" wire:click="$set('showModal', false)"
                class="max-w-xl">
                Tutup
            </x-managers.ui.button>
            <x-managers.ui.button type="button" variant="primary" type="submit" class="max-w-xl">
                Konfirmasi Pembayaran
            </x-managers.ui.button>
        </div>
    </form>
</x-managers.ui.modal>

<x-managers.ui.modal title="Perpanjang Kontrak Per Malam" :show="$showModal && $modalType === 'extend'" class="max-w-xl">
    <form wire:submit.prevent="extendContract">
        <div class="mb-4">
            <x-managers.form.label>
                Durasi Kontrak Saat Ini
            </x-managers.form.label>
            <p class="font-semibold">{{ $contract->start_date->format('d M Y') }} -
                {{ $contract->end_date->format('d M Y') }}</p>
        </div>
        <div class="mb-4">
            <x-managers.form.label>
                Tanggal Akhir Baru
            </x-managers.form.label>
            <x-managers.form.input type="date" wire:model.defer="newEndDate" name="newEndDate" />
            @error('newEndDate')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-4">
            <x-managers.form.label>
                Total Harus Dibayar
            </x-managers.form.label>
            <p class="text-green-500">
                Rp {{ number_format($extensionMustBePaid, 0, ',', '.') }}
            </p>

        </div>
        <div class="mb-4">
            <x-frontend.form.file wire:model="extensionProofOfPayment" name="extensionProofOfPayment"
                label="Pilih Bukti Pembayaran (Gambar/PDF)" />
            @error('extensionProofOfPayment')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-4">
            <x-managers.form.label>
                Jumlah Pembayaran
            </x-managers.form.label>
            <x-managers.form.input type="number" rupiah wire:model.defer="extensionAmountPaid"
                name="extensionAmountPaid" min="0" placeholder="Masukkan jumlah pembayaran" />
            @error('extensionAmountPaid')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>
        <div class="mb-6">
            <x-managers.form.label>
                Catatan Tambahan (Opsional)
            </x-managers.form.label>
            <x-managers.form.input type="text" wire:model.defer="extensionNotes" name="extensionNotes"
                placeholder="Masukkan catatan tambahan jika diperlukan" />
            @error('extensionNotes')
                <span class="text-red-500 text-xs">{{ $message }}</span>
            @enderror
        </div>

        <div class="flex justify-end mt-4 gap-2">
            <x-managers.ui.button type="button" variant="secondary" wire:click="$set('showModal', false)"
                class="max-w-xl">
                Tutup
            </x-managers.ui.button>
            <x-managers.ui.button type="submit" variant="primary" class="max-w-xl">
                Ajukan Perpanjangan
            </x-managers.ui.button>
        </div>
    </form>
</x-managers.ui.modal>

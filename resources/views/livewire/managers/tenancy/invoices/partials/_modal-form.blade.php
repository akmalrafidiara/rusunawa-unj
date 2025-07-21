<x-managers.ui.modal title="{{ $invoiceIdBeingSelected ? 'Edit' : 'Tambah' }} Tagihan" :show="$showModal && $modalType === 'form'"
    class="max-w-2xl">
    <form wire:submit.prevent="save" class="space-y-4">
        {{-- Nomor Invoice --}}
        <div>
            <x-managers.form.label>Nomor Invoice (Opsional, akan digenerate jika kosong)</x-managers.form.label>
            <x-managers.form.input wire:model="invoiceNumber" placeholder="Masukkan nomor invoice" />
        </div>

        {{-- Kontrak Terkait --}}
        <div>
            <x-managers.form.label>Kontrak Terkait</x-managers.form.label>
            <x-managers.form.select wire:model="contractId" :options="$contractOptions" label="Pilih Kontrak" required />
        </div>

        {{-- Deskripsi --}}
        <div>
            <x-managers.form.label>Deskripsi</x-managers.form.label>
            <x-managers.form.textarea wire:model="description" placeholder="Masukkan deskripsi tagihan" rows="3"
                required />
        </div>

        {{-- Jumlah --}}
        <div>
            <x-managers.form.label>Jumlah</x-managers.form.label>
            <x-managers.form.input wire:model="amount" rupiah type="number" step="0.01"
                placeholder="Masukkan jumlah tagihan" required />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Tanggal Jatuh Tempo --}}
            <div>
                <x-managers.form.label>Tanggal Jatuh Tempo</x-managers.form.label>
                <x-managers.form.input wire:model="dueAt" type="date" required />
            </div>
            {{-- Tanggal Pembayaran --}}
            <div>
                <x-managers.form.label>Tanggal Pembayaran (Opsional)</x-managers.form.label>
                <x-managers.form.input wire:model="paidAt" type="date" />
            </div>
        </div>

        {{-- Status --}}
        <div>
            <x-managers.form.label>Status Tagihan</x-managers.form.label>
            <x-managers.form.select wire:model="status" :options="$statusOptions" label="Pilih Status" required />
        </div>

        <div class="flex justify-end gap-2 pt-4">
            <x-managers.ui.button type="button" variant="secondary" wire:click="$set('showModal', false)">
                Batal
            </x-managers.ui.button>
            <x-managers.ui.button type="submit" variant="primary">
                Simpan Perubahan
            </x-managers.ui.button>
        </div>
    </form>
</x-managers.ui.modal>

{{-- Modal Form --}}
<x-managers.ui.modal title="{{ $contractIdBeingSelected ? 'Edit' : 'Tambah' }} Kontrak" :show="$showModal && $modalType === 'form'"
    class="max-w-2xl">
    <form wire:submit.prevent="save" class="space-y-4">
        {{-- Kode Kontrak --}}
        <div>
            <x-managers.form.label>Kode Kontrak (Opsional, akan digenerate jika kosong)</x-managers.form.label>
            <x-managers.form.input wire:model="contractCode" placeholder="Masukkan kode kontrak" />
        </div>

        {{-- Unit Terkait --}}
        <div>
            <x-managers.form.label>Unit Terkait</x-managers.form.label>
            <x-managers.form.select wire:model="unitId" :options="$unitOptions" label="Pilih Unit" required />
        </div>

        {{-- Penghuni Terkait (Multiple Select) --}}
        <div>
            <x-managers.form.label>Pilih Penghuni</x-managers.form.label>
            <x-managers.form.multiple-select wire:model="occupantIds" :options="$occupantOptions" />
        </div>

        {{-- Tipe Penghuni --}}
        <div>
            <x-managers.form.label>Tipe Penghuni</x-managers.form.label>
            <x-managers.form.select wire:model="occupantTypeId" :options="$occupantTypeOptions" label="Pilih Tipe Penghuni"
                required />
        </div>

        {{-- Total Harga --}}
        <div>
            <x-managers.form.label>Total Harga</x-managers.form.label>
            <x-managers.form.input wire:model="totalPrice" type="number" step="0.01"
                placeholder="Masukkan total harga" required />
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            {{-- Tanggal Mulai --}}
            <div>
                <x-managers.form.label>Tanggal Mulai Kontrak</x-managers.form.label>
                <x-managers.form.input wire:model="startDate" type="date" required />
            </div>
            {{-- Tanggal Berakhir --}}
            <div>
                <x-managers.form.label>Tanggal Berakhir Kontrak</x-managers.form.label>
                <x-managers.form.input wire:model="endDate" type="date" required />
            </div>
        </div>

        {{-- Dasar Harga --}}
        <div>
            <x-managers.form.label>Dasar Harga</x-managers.form.label>
            <x-managers.form.select wire:model="pricingBasis" rupiah :options="$pricingBasisOptions" label="Pilih Dasar Harga"
                required />
        </div>

        {{-- Status Kontrak --}}
        <div>
            <x-managers.form.label>Status Kontrak</x-managers.form.label>
            <x-managers.form.select wire:model="status" :options="$statusOptions" label="Pilih Status" required />
        </div>

        {{-- Status Kunci --}}
        <div>
            <x-managers.form.label>Status Kunci</x-managers.form.label>
            <x-managers.form.select wire:model="keyStatus" :options="$keyStatusOptions" label="Pilih Status Kunci" />
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

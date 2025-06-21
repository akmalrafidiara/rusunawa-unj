{{-- Modal Form --}}
<x-managers.ui.modal title="Form Tipe Penghuni" :show="$showModal" class="max-w-md">
    <form wire:submit.prevent="save" class="space-y-4">
        <!-- name -->
        <x-managers.form.label>Nama</x-managers.form.label>
        <x-managers.form.input wire:model.live="name" placeholder="Tipe Penghuni" />

        {{-- Description --}}
        <x-managers.form.label>Deskripsi</x-managers.form.label>
        <x-managers.form.textarea wire:model.live="description" placeholder="Deskripsikan tipe penghuni ini"
            rows="3" />

        {{-- Accessible Clusters --}}
        <x-managers.form.label>Unit Cluster yang Dapat Diakses</x-managers.form>
            <div class="grid grid-cols-1 gap-3">
                @foreach ($unitClusterOptions as $value => $label)
                    <label for="cluster_{{ $value }}"
                        class="flex items-center p-3 bg-white rounded-md border border-gray-200 hover:border-blue-300 hover:bg-blue-50 cursor-pointer transition-all duration-200">
                        <input type="checkbox" wire:model.live="accessibleClusters" value="{{ $value }}"
                            id="cluster_{{ $value }}"
                            class="w-4 h-4 rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-3 text-sm font-medium text-gray-700">{{ $label }}</span>
                    </label>
                @endforeach
            </div>

            {{-- Requires Verification --}}
            <x-managers.form.label>Memerlukan Verifikasi</x-managers.form.label>
            <x-managers.form.checkbox wire:model.live="requiresVerification" label="Ya, memerlukan verifikasi"
                description="Jika diaktifkan, harga ini memerlukan verifikasi sebelum diterapkan." />

            <!-- Tombol Aksi -->
            <div class="flex justify-end gap-2 mt-10">
                <x-managers.ui.button type="button" variant="secondary"
                    wire:click="$set('showModal', false)">Batal</x-managers.ui.button>
                <x-managers.ui.button wire:click="save()" variant="primary">
                    Simpan
                </x-managers.ui.button>
            </div>
    </form>
</x-managers.ui.modal>

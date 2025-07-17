<x-managers.ui.modal title="Form User" :show="$showModal" class="max-w-md">
    <form wire:submit.prevent="save" class="space-y-4">

        {{-- Name --}}
        <x-managers.form.label>Nama Lengkap</x-managers.form.label>
        <x-managers.form.input wire:model.live="name" placeholder="Nama Lengkap" type="text" />

        {{-- Email --}}
        <x-managers.form.label>Email</x-managers.form.label>
        <x-managers.form.input wire:model.live="email" placeholder="Email" type="email" />

        {{-- Password --}}
        <x-managers.form.label>Password</x-managers.form.label>
        <x-managers.form.input wire:model.live="password" placeholder="Password" type="password" />

        {{-- Phone --}}
        <x-managers.form.label>No. Telepon</x-managers.form.label>
        <x-managers.form.input wire:model.live="phone" placeholder="Phone Number" type="text" />

        {{-- Role --}}
        <x-managers.form.label>Role</x-managers.form.label>
        <x-managers.form.select wire:model.live="role" :options="$roleOptions" label="Role" />

        {{-- Assigned Clusters (conditional for Staff Rusunawa) --}}
        @if ($role === \App\Enums\RoleUser::STAFF_OF_RUSUNAWA->value)
            <x-managers.form.label>Ditugaskan di Gedung (Bisa pilih lebih dari satu)</x-managers.form.label>
            <div class="grid grid-cols-1 gap-3">
                @foreach ($unitClusterOptions as $option)
                    <label for="cluster_{{ $option['value'] }}"
                        class="flex items-center p-3 bg-white rounded-md border border-gray-200 hover:border-blue-300 hover:bg-blue-50 cursor-pointer transition-all duration-200 dark:bg-zinc-700 dark:border-zinc-600 dark:hover:bg-zinc-600">
                        <input type="checkbox" wire:model.live="assignedClusters" value="{{ $option['value'] }}"
                            id="cluster_{{ $option['value'] }}"
                            class="w-4 h-4 rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 dark:bg-zinc-800 dark:border-zinc-500">
                        <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">{{ $option['label'] }}</span>
                    </label>
                @endforeach
            </div>
            @error('assignedClusters')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
            @error('assignedClusters.*')
                <span class="text-red-500 text-sm">{{ $message }}</span>
            @enderror
        @endif

        <div class="flex justify-end gap-2">
            <x-managers.ui.button type="button" variant="secondary"
                wire:click="$set('showModal', false)">Batal</x-managers.ui.button>
            <x-managers.ui.button wire:click="save()" type="submit" variant="primary">Simpan</x-managers.ui.button>
        </div>
    </form>
</x-managers.ui.modal>
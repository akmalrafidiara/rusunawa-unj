<x-managers.ui.card-side class="p-4 h-full flex flex-col">
    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Daftar Permintaan</h3>

    {{-- Tabs --}}
    <div class="flex border-b border-gray-200 dark:border-zinc-700 mb-4">
        {{-- <button wire:click="$set('tab', 'aktif')"
            class="flex-1 px-4 py-2 text-sm font-medium transition-colors duration-200 ease-in-out text-center relative
                       {{ $tab === 'aktif' ? 'text-green-600 dark:text-green-400' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white' }}">
            Aktif
            @if ($tab === 'aktif')
                <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-green-500"></div>
            @endif
        </button>
        <button wire:click="$set('tab', 'selesai')"
            class="flex-1 px-4 py-2 text-sm font-medium transition-colors duration-200 ease-in-out text-center relative
                       {{ $tab === 'selesai' ? 'text-green-600 dark:text-green-400' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white' }}">
            Selesai
            @if ($tab === 'selesai')
                <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-green-500"></div>
            @endif --}}
        </button>
    </div>

    <div class="flex flex-col sm:flex-row gap-4 mb-6">
        <x-managers.form.input wire:model.live.debounce.300ms="search" clearable
            placeholder="Cari Nama atau Id Kontrak..." icon="magnifying-glass" class="w-full" />
        <div class="flex gap-4">
            <x-managers.ui.dropdown class="flex flex-col gap-2">
                <x-slot name="trigger">
                    <flux:icon.adjustments-horizontal />
                </x-slot>
                <x-managers.form.small>Filter Status</x-managers.form.small>
                {{-- <div class="flex gap-2">
                    <x-managers.ui.dropdown-picker wire:model.live="statusFilter" :options="$statusOptions"
                        label="Semua Status" />
                </div> --}}
            </x-managers.ui.dropdown>
        </div>
    </div>

    {{-- Daftar laporan --}}
    <div class="flex flex-col gap-4 overflow-y-auto pr-2" style="max-height: 70vh;">
        @forelse ($occupants as $occupant)
            <div wire:click="selectOccupant({{ $occupant->id }})"
                class="p-6 rounded-lg border cursor-pointer transition-colors duration-200
                {{ $occupantIdBeingSelected === $occupant->id ? 'bg-green-100 border-green-500 dark:bg-green-900/30 dark:border-green-700' : 'bg-gray-50 border-gray-200 hover:bg-gray-100 dark:bg-zinc-700 dark:border-zinc-600 dark:hover:bg-zinc-600' }}">

                {{-- Occupant Name --}}
                <div class="font-medium text-gray-800 dark:text-gray-200">
                    {{ $occupant->full_name }}
                </div>

                {{-- Created At --}}
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    {{ $occupant->created_at->translatedFormat('d F Y, H:i') }} WIB
                    <span class="text-xs text-gray-500 dark:text-gray-500 ml-2">
                        ({{ $occupant->created_at->diffForHumans() }})
                    </span>
                </div>
            </div>
        @empty
            <p class="text-center text-gray-500 dark:text-gray-400 py-6">Tidak ada penghuni yang perlu diverifikasi.</p>
        @endforelse
    </div>
    <x-managers.ui.pagination :paginator="$occupants" class="mt-4" />
</x-managers.ui.card-side>

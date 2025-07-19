<x-managers.ui.card-side class="p-4 h-full flex flex-col">
    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Daftar Permintaan</h3>

    {{-- Tabs --}}
    <div class="flex border-b border-gray-200 dark:border-zinc-700 mb-4">
        <button wire:click="$set('tab', 'recent')"
            class="cursor-pointer flex-1 px-4 py-2 text-sm font-medium transition-colors duration-200 ease-in-out text-center relative
                            {{ $tab === 'recent' ? 'text-green-600 dark:text-green-400' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white' }}">
            Terbaru
            @if ($tab === 'recent')
                <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-green-500"></div>
            @endif
        </button>
        <button wire:click="$set('tab', 'history')"
            class="cursor-pointer flex-1 px-4 py-2 text-sm font-medium transition-colors duration-200 ease-in-out text-center relative
                            {{ $tab === 'history' ? 'text-green-600 dark:text-green-400' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-white' }}">
            History
            @if ($tab === 'history')
                <div class="absolute bottom-0 left-0 right-0 h-0.5 bg-green-500"></div>
            @endif
        </button>
    </div>

    @if ($tab === 'recent')
        <div class="flex flex-col sm:flex-row gap-4 mb-6">
            <x-managers.form.input wire:model.live.debounce.300ms="search" clearable
                placeholder="Cari Nama atau Id Kontrak..." icon="magnifying-glass" class="w-full" />
        </div>
    @endif

    {{-- Content berdasarkan tab yang aktif --}}
    {{-- Menghapus wire:poll karena permintaan pengguna --}}
    <div class="flex flex-col gap-4 overflow-y-auto pr-2" style="max-height: 70vh;">
        @if ($tab === 'recent')
            @forelse ($recentOccupants as $occupant)
                {{-- Menggunakan variabel yang dilewatkan dari render() --}}
                <div wire:click="selectOccupant({{ $occupant->id }})"
                    class="p-6 rounded-lg border cursor-pointer transition-colors duration-200
                    {{ $occupantIdBeingSelected === $occupant->id ? 'bg-green-100 border-green-500 dark:bg-green-900/30 dark:border-green-700' : 'bg-gray-50 border-gray-200 hover:bg-gray-100 dark:bg-zinc-700 dark:border-zinc-600 dark:hover:bg-zinc-600' }}">

                    {{-- Occupant Name --}}
                    <div class="font-medium text-gray-800 dark:text-gray-200">
                        {{ $occupant->full_name }}
                    </div>

                    {{-- Created At --}}
                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        {{ $occupant->updated_at->format('d F Y, H:i') }} WIB
                        <span class="text-xs text-gray-500 dark:text-gray-500 ml-2">
                            ({{ $occupant->updated_at->diffForHumans() }})
                        </span>
                    </div>
                </div>
            @empty
                <p class="text-center text-gray-500 dark:text-gray-400 py-6">Tidak ada penghuni yang perlu diverifikasi.
                </p>
            @endforelse
        @elseif ($tab === 'history')
            @forelse ($historyLogs as $history)
                <div wire:click="selectOccupant({{ $history->loggable->id }})"
                    class="p-6 flex flex-col gap-2 rounded-lg border cursor-pointer transition-colors duration-200
                    {{ $occupantIdBeingSelected === $history->loggable->id ? 'bg-green-100 border-green-500 dark:bg-green-900/30 dark:border-green-700' : 'bg-gray-50 border-gray-200 hover:bg-gray-100 dark:bg-zinc-700 dark:border-zinc-600 dark:hover:bg-zinc-600' }}">

                    {{-- Log Detail --}}
                    <div class="flex justify-between font-medium text-gray-800 dark:text-gray-200">
                        {{ $history->loggable->full_name ?? 'N/A' }}
                        <span
                            class="ml-2 text-xs px-2 py-1 rounded-full
                            {{ $history->status === 'approved' ? 'bg-green-200 text-green-800 dark:bg-green-700 dark:text-green-100' : 'bg-red-200 text-red-800 dark:bg-red-700 dark:text-red-100' }}">
                            {{ ucfirst($history->status) }}
                        </span>
                    </div>

                    <div class="text-sm text-gray-600 dark:text-gray-400">
                        <p>Diproses oleh: <span
                                style="font-semibold">{{ $history->processor->name ?? 'System' }}</span></p>
                        <p>Pada {{ $history->processed_at->format('d F Y, H:i') }} WIB <span
                                class="text-xs text-gray-500 dark:text-gray-500 ml-2">
                                ({{ $history->processed_at->diffForHumans() }})
                            </span></p>
                    </div>
                    @if ($history->reason)
                        <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            Alasan: "{{ $history->reason }}"
                        </div>
                    @endif
                </div>
            @empty
                <p class="text-center text-gray-500 dark:text-gray-400 py-6">Tidak ada riwayat verifikasi penghuni.</p>
            @endforelse
        @endif
    </div>
    <x-managers.ui.pagination :paginator="$paginator" class="mt-4" /> {{-- Menggunakan paginator umum --}}
</x-managers.ui.card-side>

{{-- Isi dari _sidebar-schedule-list.blade.php --}}
<div class="flex justify-between items-center mb-4">
    <h4 class="text-lg font-bold text-gray-800 dark:text-white">Daftar Kamar AC</h4>
    @if (!$is_admin_user)
        <x-managers.ui.button wire:click="createSchedule" variant="primary" size="sm">
            Buat Jadwal AC Baru
        </x-managers.ui.button>
    @endif
</div>
<div class="flex flex-col gap-3 overflow-y-auto pr-2" style="max-height: 70vh;">
    @forelse ($schedules as $schedule)
        <div wire:key="list-schedule-{{ $schedule->id }}"
             wire:click="$set('selectedScheduleId', {{ $schedule->id }}); $dispatch('recordHistoryUpdated')"
             class="p-4 rounded-lg border cursor-pointer transition-colors duration-200
                {{ $selectedScheduleId === $schedule->id ? 'bg-green-100 border-green-500 dark:bg-green-900/30 dark:border-green-700' : 'bg-gray-50 border-gray-200 hover:bg-gray-100 dark:bg-zinc-700 dark:border-zinc-600 dark:hover:bg-zinc-600' }}">
            <div class="flex justify-between items-center mb-1">
                <span class="font-semibold text-lg text-gray-900 dark:text-white">Kamar {{ $schedule->unit->room_number }}</span>
                <div class="flex items-center gap-2">
                    <x-managers.ui.badge :color="$schedule->status->color()">
                        {{ $schedule->status->label() }}
                    </x-managers.ui.badge>
                </div>
            </div>
            <p class="text-sm text-gray-700 dark:text-gray-300">
                Gedung: <span class="font-medium">{{ optional($schedule->unit->unitCluster)->name ?: '-' }}</span>
            </p>
            <p class="text-sm text-gray-700 dark:text-gray-300">
                Pemeliharaan Selanjutnya:
                <span class="font-medium">{{ \Carbon\Carbon::parse($schedule?->next_due_date)?->format('d F Y') }}</span>
            </p>
            <p class="text-xs text-gray-600 dark:text-gray-400">
                Frekuensi: {{ $schedule->frequency_months }} Bulan Sekali
            </p>
        </div>
    @empty
        <p class="text-center text-gray-500 dark:text-gray-400">Tidak ada jadwal pemeliharaan AC ditemukan.</p>
    @endforelse
</div>
<x-managers.ui.pagination :paginator="$schedules" class="mt-2" />
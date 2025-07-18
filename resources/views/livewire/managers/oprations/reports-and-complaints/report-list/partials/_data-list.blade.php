{{-- Daftar laporan --}}
<div class="flex flex-col gap-4 overflow-y-auto pr-2" style="max-height: 70vh;">
    @forelse ($reports as $report)
        <div wire:key="list-report-{{ $report->id }}" wire:click="selectReport({{ $report->id }})"
            class="p-6 rounded-lg border cursor-pointer transition-colors duration-200
                {{ $selectedReportId === $report->id ? 'bg-green-100 border-green-500 dark:bg-green-900/30 dark:border-green-700' : 'bg-gray-50 border-gray-200 hover:bg-gray-100 dark:bg-zinc-700 dark:border-zinc-600 dark:hover:bg-zinc-600' }}">

            {{-- Status --}}
            <div class="mb-3">
                <x-managers.ui.badge :color="$this->getReportDisplayStatus($report)->color()">
                    {{ $this->getReportDisplayStatus($report)->label() }}
                </x-managers.ui.badge>
            </div>

            {{-- Konten --}}
            <div>
                <p class="text-lg text-gray-800 dark:text-gray-200 font-semibold mb-2">
                    {{ \Illuminate\Support\Str::words($report->subject, 20, ' ...') }}
                </p>
                <div class="text-sm text-gray-600 dark:text-gray-400">
                    <p class="font-medium">
                        Kamar {{ $report->contract->unit->room_number ?? 'N/A' }}
                        <span class="text-gray-400 dark:text-gray-500 mx-1">|</span>
                        {{ $report->unique_id }}
                    </p>
                    <p class="mt-2 text-xs">
                        {{ $report->created_at->translatedFormat('d F Y, H:i') }} WIB
                    </p>
                </div>
            </div>
        </div>
    @empty
        <p class="text-center text-gray-500 dark:text-gray-400 py-6">Tidak ada laporan ditemukan.</p>
    @endforelse
</div>
<x-managers.ui.pagination :paginator="$reports" class="mt-4" />

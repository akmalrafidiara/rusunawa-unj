{{-- Informasi Laporan --}}
<div class="border border-gray-200 dark:border-zinc-700 rounded-lg p-4 bg-gray-50 dark:bg-zinc-700 mb-4">
    <h4 class="text-lg font-semibold text-zinc-800 dark:text-zinc-100 mb-4 flex items-center gap-2">
        <flux:icon.information-circle class="w-5 h-5 text-blue-500 dark:text-blue-400" />
        Informasi Laporan
    </h4>
    <div class="space-y-3">
        <div class="flex justify-between items-center py-2 border-b border-zinc-100 dark:border-zinc-700">
            <span class="text-zinc-600 dark:text-zinc-300">Subjek</span>
            <span class="font-semibold text-zinc-800 dark:text-zinc-100">{{ $reportSubject }}</span>
        </div>
        <div class="flex justify-between items-center py-2">
            <span class="text-zinc-600 dark:text-zinc-300">Nomor Kamar</span>
            <span class="font-mono text-sm bg-zinc-100 dark:bg-zinc-700 text-zinc-800 dark:text-zinc-200 px-2 py-1 rounded">
                {{ $reportRoomNumber }} ({{ $reportUnitClusterName }})
            </span>
        </div>
        <div class="flex justify-between items-center py-2 border-b border-zinc-100 dark:border-zinc-700">
            <span class="text-zinc-600 dark:text-zinc-300">Dilaporkan Oleh</span>
            <span class="font-semibold text-zinc-800 dark:text-zinc-100">{{ $reportReporterName }} ({{ $reportReporterType }})</span>
        </div>
        <div class="flex justify-between items-center py-2 border-b border-zinc-100 dark:border-zinc-700">
            <span class="text-zinc-600 dark:text-zinc-300">Nomor Telepon Pelapor</span>
            <span class="font-semibold text-zinc-800 dark:text-zinc-100">{{ $reportReporterPhone }}</span>
        </div>
        <div class="flex justify-between items-center py-2 border-b border-zinc-100 dark:border-zinc-700">
            <span class="text-zinc-600 dark:text-zinc-300">Email Pelapor</span>
            <span class="font-semibold text-zinc-800 dark:text-zinc-100">{{ $reportReporterEmail }}</span>
        </div>
    </div>
</div>
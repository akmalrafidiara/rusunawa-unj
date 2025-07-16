{{-- Deskripsi dan Lampiran --}}
<div class="border border-gray-200 dark:border-zinc-700 rounded-lg p-4 bg-gray-50 dark:bg-zinc-700">
    <h4 class="text-lg font-semibold text-zinc-800 dark:text-zinc-100 mb-4 flex items-center gap-2">
        <flux:icon.document-text class="w-5 h-5 text-orange-500 dark:text-orange-400" />
        Deskripsi Laporan
    </h4>
    <p class="text-zinc-800 dark:text-zinc-200 leading-relaxed">{{ $reportDescription }}</p>

    @if ($reportInitialAttachments && count($reportInitialAttachments) > 0)
        <div class="border-t border-zinc-200 dark:border-zinc-600 my-4"></div>
        <h4 class="text-lg font-semibold text-zinc-800 dark:text-zinc-100 mb-4 flex items-center gap-2">
            <flux:icon.paper-clip class="w-5 h-5 text-teal-500 dark:text-teal-400" />
            Lampiran Laporan Awal
        </h4>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
            @foreach ($reportInitialAttachments as $attachment)
            <a href="{{ Storage::url($attachment['path']) }}" target="_blank" class="block">
                @if (Str::startsWith($attachment['mime_type'], 'image'))
                <img src="{{ Storage::url($attachment['path']) }}" class="w-full h-24 object-cover rounded-lg shadow-sm border border-gray-200 dark:border-zinc-600" alt="{{ $attachment['name'] }}">
                @else
                <div class="w-full h-24 flex flex-col items-center justify-center bg-gray-100 rounded-lg shadow-sm text-gray-500 dark:bg-zinc-800 dark:text-gray-400 border border-gray-200 dark:border-zinc-600">
                    <flux:icon.document class="w-8 h-8 mb-1" />
                    <p class="text-xs text-center px-1 truncate w-full">{{ $attachment['name'] }}</p>
                </div>
                @endif
            </a>
            @endforeach
        </div>
    @endif
</div>
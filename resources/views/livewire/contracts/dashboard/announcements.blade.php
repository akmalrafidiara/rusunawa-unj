<div class="bg-white dark:bg-zinc-800 rounded-lg shadow-md border dark:border-zinc-700 p-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-bold">Pengumuman</h3>
        <a href="{{ route('announcement.index') }}" class="text-emerald-500 hover:underline text-sm font-medium">Lihat Semua →</a>
    </div>
    <div class="space-y-4">
        @forelse ($announcements as $item)
            <div class="border-b dark:border-zinc-700 pb-3 last:border-b-0 last:pb-0">
                <div class="flex justify-between items-start">
                    <p class="font-semibold">{{ $item->title }}</p>
                    <x-managers.ui.badge :color="$item->category->color()">
                        {{ $item->category->label() }}
                    </x-managers.ui.badge>
                </div>
                <p class="text-xs text-gray-500 mb-1">{{ $item->created_at->translatedFormat('d M Y') }}</p>
                <p class="text-sm text-gray-600 dark:text-gray-300">
                    {{ Str::limit(strip_tags($item->description), 100) }}
                </p>
            </div>
        @empty
            <p class="text-gray-500 dark:text-gray-400">Tidak ada pengumuman terbaru.</p>
        @endforelse
    </div>
</div>
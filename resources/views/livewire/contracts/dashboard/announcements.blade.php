<div class="bg-white dark:bg-zinc-800 rounded-lg shadow-md border dark:border-zinc-700 p-6">
    <div class="flex justify-between items-center mb-4">
        <h3 class="text-xl font-bold">Pengumuman</h3>
        <a href="{{ route('announcement.index') }}" class="text-emerald-500 hover:underline text-sm font-medium">Lihat Semua →</a>
    </div>
    <div class="space-y-3">
        @forelse ($announcements as $announcement)
            <a href="{{ route('announcement.show', $announcement) }}" class="block w-full">
                <div class="relative border dark:border-zinc-700 bg-gray-50 dark:bg-zinc-600 p-4 rounded-lg hover:bg-gray-100 dark:hover:bg-zinc-700 transition duration-200">
                    
                    {{--  tanggal dan badge --}}
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-xs text-gray-500 dark:text-gray-400">{{ $announcement->created_at->format('d M Y') }}</span>
                        <x-managers.ui.badge :color="$announcement->category->color()">
                            {{ $announcement->category->label() }}
                        </x-managers.ui.badge>
                    </div>
                    
                    {{-- Judul pengumuman --}}
                    <p class="font-semibold text-gray-800 dark:text-white mt-1">{{ $announcement->title }}</p>
                    
                    {{-- Ringkasan deskripsi --}}
                    <p class="text-sm text-gray-600 dark:text-gray-300">{{ Str::limit(strip_tags($announcement->description), 300) }}</p>

                </div>
            </a>
        @empty
            <div class="relative border dark:border-zinc-700 bg-gray-50 dark:bg-zinc-700/50 p-4 rounded-lg">
                 <p class="text-gray-500 dark:text-gray-400">Tidak ada pengumuman.</p>
            </div>
        @endforelse
    </div>
</div>
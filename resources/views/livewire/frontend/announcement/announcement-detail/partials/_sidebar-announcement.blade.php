<div class="lg:w-1/3 sticky top-8 lg:pr-5">
    <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-lg p-6">
        <h2 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-4 pb-2 border-b-2 border-gray-200 dark:border-zinc-700">Pengumuman Lainnya</h2>
        @if ($relatedAnnouncements->isEmpty())
        <p class="text-gray-500 dark:text-gray-400 text-sm">Tidak ada pengumuman terkait lainnya.</p>
        @else
        <ul class="space-y-4">
            @foreach ($relatedAnnouncements as $related)
            <li class="border-b border-gray-200 dark:border-zinc-700 pb-4 last:border-b-0 last:pb-0">
                <a href="{{ route('announcement.show', ['slug' => $related->slug]) }}" class="block hover:bg-gray-50 dark:hover:bg-zinc-700 -mx-2 px-2 py-1 rounded-md transition duration-150 ease-in-out">
                    <h3 class="text-md font-semibold text-gray-800 dark:text-gray-200 leading-tight">{{ \Str::limit($related->title, 100) }}</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1 flex items-center">
                        <flux:icon name="calendar" class="h-4 w-4 text-gray-400 dark:text-gray-500 mr-1" />
                        @if ($related->created_at->diffInDays() <= 7)
                            {{ $related->created_at->diffForHumans() }}
                            @else
                            {{ $related->created_at->format('d M Y H:i') }}
                            @endif
                    </p>
                </a>
            </li>
            @endforeach
        </ul>
        @endif
        {{-- Tombol Lihat Pengumuman Lainnya --}}
        <div class="mt-6 text-center">
            <a href="{{ route('announcement.index') }}"
                class="inline-flex items-center justify-center px-5 py-2 border border-transparent
          text-base font-medium rounded-md text-white bg-green-600 hover:bg-green-700
          dark:bg-white dark:text-zinc-800 dark:hover:bg-gray-200
          transition duration-150 ease-in-out shadow-md w-full">
                Lihat Semua Pengumuman
                <flux:icon name="arrow-right" class="ml-2 -mr-1 w-4 h-4" />
            </a>
        </div>
    </div>
</div>
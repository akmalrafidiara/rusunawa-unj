@if ($announcements->isEmpty())
    <div class="bg-white dark:bg-zinc-800 p-6 rounded-lg shadow-lg text-center text-gray-600 dark:text-gray-300">
        <p>No published announcements found matching your criteria.</p>
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
        @foreach ($announcements as $announcement)
            <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-lg overflow-hidden flex flex-col h-full">
                {{-- Container for image and category badge --}}
                <div class="relative w-full h-48">
                    @if ($announcement->image)
                        <img class="w-full h-full object-cover object-center" src="{{ Storage::url($announcement->image) }}" alt="{{ $announcement->title }}">
                    @else
                        <div class="w-full h-full bg-gray-200 dark:bg-zinc-700 flex items-center justify-center text-gray-500 dark:text-gray-400">
                            No Image Available
                        </div>
                    @endif

                    {{-- Category Badge - Positioned absolutely --}}
                    @php
                        $categoryEnum = \App\Enums\AnnouncementCategory::fromValue($announcement->category->value);
                        $categoryColorClasses = $categoryEnum ? implode(' ', $categoryEnum->color()) : 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-100';
                    @endphp
                    <span class="absolute top-2 right-2 px-3 py-1 text-xs font-semibold rounded-full z-10 {{ $categoryColorClasses }}">
                        {{ $announcement->category->label() }}
                    </span>
                </div>

                <div class="p-6 flex flex-col flex-grow">
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-100 mb-2">
                        {!! strip_tags(\Illuminate\Support\Str::limit($announcement->title,75)) !!}
                    </h2>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs text-gray-500 dark:text-gray-400">
                            @if ($announcement->created_at->diffInDays() <= 7)
                                {{ $announcement->created_at->diffForHumans() }}
                            @else
                                {{ $announcement->created_at->format('d M Y H:i') }}
                            @endif
                        </span>
                    </div>
                    <p class="text-gray-600 dark:text-gray-300 text-sm mb-4 flex-grow line-clamp-3">
                        {!! strip_tags(\Illuminate\Support\Str::limit($announcement->description, 150)) !!}
                    </p>
                    <div class="mt-auto">
                        <a href="{{ route('announcement.show', $announcement->id) }}"
                            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 focus:outline-none focus:border-green-700 
                                dark:bg-white dark:text-zinc-800 dark:hover:bg-zinc-200 dark:focus:border-gray-700 focus:ring focus:ring-green-200 active:bg-green-600 disabled:opacity-25 transition ease-in-out duration-150">
                            Baca Selengkapnya
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Pagination --}}
    <x-frontend.pagination :paginator="$announcements" />
@endif
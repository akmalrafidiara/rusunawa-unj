<!-- Recent Announcements -->
@if ($recentAnnouncements && $recentAnnouncements->count() > 0)
    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white">Recent Announcements</h3>
                <a href="{{ route('announcements') }}" class="text-sm text-blue-600 hover:text-blue-700">View
                    all</a>
            </div>
            <div class="space-y-4">
                @foreach ($recentAnnouncements as $announcement)
                    <div class="border-l-4 border-blue-400 pl-4">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $announcement->title }}</h4>
                            <span
                                class="text-xs text-gray-500 dark:text-gray-400">{{ $announcement->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                            {{ Str::limit($announcement->description, 100) }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif

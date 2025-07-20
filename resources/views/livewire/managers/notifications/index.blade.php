@if (request()->routeIs('managers.notifications'))
    @include('livewire.managers.notifications.full-page')
@else
    <div class="relative" x-data="{ open: false }" wire:poll.5s>
        <button @click="open = !open"
            class="relative text-gray-600 dark:text-gray-300 hover:text-gray-800 dark:hover:text-white focus:outline-none">
            <flux:icon.bell class="mt-2 h-6 w-6" />
            @if ($unreadCount > 0)
                <span class="absolute top-1 -right-1 flex h-4 w-4">
                    <span
                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                    <span
                        class="relative inline-flex rounded-full h-4 w-4 bg-red-500 text-white text-xs items-center justify-center">{{ $unreadCount }}</span>
                </span>
            @endif
        </button>

        <div x-show="open" @click.away="open = false" x-transition
            class="absolute right-0 mt-2 w-80 bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-lg shadow-lg z-20">
            <div class="p-4 border-b border-gray-200 dark:border-zinc-700 flex justify-between items-center">
                <h6 class="font-semibold text-gray-800 dark:text-gray-200">Notifikasi</h6>
                @if ($unreadCount > 0)
                    <button wire:click="markAllAsRead" class="text-sm text-blue-500 hover:underline">Tandai semua
                        dibaca</button>
                @endif
            </div>
            <div class="max-h-96 overflow-y-auto">
                @forelse($notifications as $notification)
                    <div
                        class="block p-4 border-b border-gray-100 dark:border-zinc-700 hover:bg-gray-100 dark:hover:bg-zinc-700">
                        <a href="#"
                            wire:click.prevent="readAndRedirect('{{ $notification->id }}', '{{ $notification->data['url'] ?? '#' }}')">
                            <p class="text-sm text-gray-700 dark:text-gray-300">{{ $notification->data['message'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                {{ $notification->created_at->diffForHumans() }}</p>
                        </a>
                        @if (is_null($notification->read_at))
                            <button wire:click="markAsRead('{{ $notification->id }}')"
                                class="mt-2 text-xs text-blue-500 hover:underline">Tandai dibaca</button>
                        @endif
                    </div>
                @empty
                    <div class="p-4 text-center text-gray-500 dark:text-gray-400">
                        Tidak ada notifikasi.
                    </div>
                @endforelse
            </div>
            @if ($notifications->count() > 0)
                <div class="p-3 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                    <a href="{{ route('managers.notifications') }}"
                        class="block w-full text-center text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                        Lihat semua notifikasi
                    </a>
                </div>
            @endif
        </div>
    </div>
@endif

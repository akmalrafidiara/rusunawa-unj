@if (request()->routeIs('managers.notifications'))
    @include('livewire.managers.notifications.full-page')
@else
    <div class="relative" x-data="{ open: false }" wire:poll.5s>
        <flux:button @click="open = !open" variant="ghost" size="sm" class="relative">
            <flux:icon.bell class="h-5 w-5" />
            @if ($unreadCount > 0)
                <span class="absolute -top-1 -right-1 flex h-4 w-4">
                    <span
                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                    <span
                        class="relative inline-flex rounded-full h-4 w-4 bg-red-500 text-white text-xs items-center justify-center font-medium">
                        {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                    </span>
                </span>
            @endif
        </flux:button>

        <div x-show="open" @click.away="open = false" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="absolute right-0 mt-2 w-96 bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 rounded-xl shadow-xl z-50">
            <!-- Header -->
            <div class="p-4 border-b border-zinc-200 dark:border-zinc-700">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Notifikasi</h3>
                    @if ($unreadCount > 0)
                        <flux:button wire:click="markAllAsRead" variant="ghost" size="sm"
                            class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                            Tandai semua dibaca
                        </flux:button>
                    @endif
                </div>
                @if ($unreadCount > 0)
                    <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">
                        {{ $unreadCount }} notifikasi belum dibaca
                    </p>
                @endif
            </div>

            <!-- Content -->
            <div class="max-h-96 overflow-y-auto">
                @forelse($notifications as $notification)
                    <div
                        class="group p-4 border-b border-zinc-100 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors duration-150 {{ $notification->unread() ? 'bg-blue-50/50 dark:bg-blue-900/10' : '' }}">
                        <div class="flex items-start gap-3">
                            <!-- Icon -->
                            <div class="flex-shrink-0">
                                <div
                                    class="w-8 h-8 {{ $this->getNotificationColor($notification->data['color'] ?? 'blue') }} rounded-lg flex items-center justify-center">
                                    <flux:icon
                                        name="{{ $this->getNotificationIcon($notification->data['icon'] ?? 'bell') }}"
                                        class="w-4 h-4 text-white" />
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex-1">
                                        <p
                                            class="text-sm font-medium text-zinc-900 dark:text-zinc-100 line-clamp-2 group-hover:text-zinc-700 dark:group-hover:text-zinc-200">
                                            {{ $notification->data['message'] }}
                                        </p>
                                        <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-1">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                    @if ($notification->unread())
                                        <div class="flex-shrink-0">
                                            <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Action buttons -->
                                <div
                                    class="flex items-center gap-2 mt-2 opacity-0 group-hover:opacity-100 transition-opacity duration-150">
                                    @if (isset($notification->data['url']))
                                        <flux:button
                                            wire:click="readAndRedirect('{{ $notification->id }}', '{{ $notification->data['url'] }}')"
                                            variant="ghost" size="xs"
                                            class="text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                                            Lihat Detail
                                        </flux:button>
                                    @endif
                                    @if ($notification->unread())
                                        <flux:button wire:click="markAsRead('{{ $notification->id }}')" variant="ghost"
                                            size="xs"
                                            class="text-zinc-600 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-300">
                                            Tandai dibaca
                                        </flux:button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center">
                        <div class="w-16 h-16 mx-auto mb-4 text-zinc-300 dark:text-zinc-600">
                            <flux:icon.bell class="w-full h-full" />
                        </div>
                        <h3 class="text-sm font-medium text-zinc-900 dark:text-zinc-100 mb-1">Tidak ada notifikasi</h3>
                        <p class="text-xs text-zinc-500 dark:text-zinc-400">Notifikasi baru akan muncul di sini</p>
                    </div>
                @endforelse
            </div>

            <!-- Footer -->
            @if ($notifications->count() > 0)
                <div class="p-3 border-t border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800">
                    <flux:button href="{{ route('managers.notifications') }}" variant="ghost" size="sm"
                        class="w-full justify-center text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300"
                        wire:navigate>
                        Lihat semua notifikasi
                        <flux:icon.arrow-right class="w-4 h-4 ml-1" />
                    </flux:button>
                </div>
            @endif
        </div>
    </div>
@endif

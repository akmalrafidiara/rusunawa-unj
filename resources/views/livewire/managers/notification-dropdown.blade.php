<div class="relative" x-data="{ open: @entangle('isOpen') }">
    <!-- Notification Bell Button -->
    <button @click="open = !open"
        class="relative p-2 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-lg transition-colors">
        <flux:icon.bell class="w-6 h-6" />
        @if ($unreadCount > 0)
            <span
                class="absolute -top-1 -right-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white bg-red-600 rounded-full min-w-[20px] h-5">
                {{ $unreadCount > 99 ? '99+' : $unreadCount }}
            </span>
        @endif
    </button>

    <!-- Dropdown Panel -->
    <div x-show="open" x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100"
        x-transition:leave-end="transform opacity-0 scale-95" @click.away="open = false"
        class="absolute right-0 z-50 mt-2 w-80 bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700"
        style="display: none;">
        <!-- Header -->
        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Notifications</h3>
                @if ($unreadCount > 0)
                    <button wire:click="markAllAsRead"
                        class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                        Mark all as read
                    </button>
                @endif
            </div>
        </div>

        <!-- Notifications List -->
        <div class="max-h-96 overflow-y-auto">
            @forelse($notifications as $notification)
                <div class="px-4 py-3 border-b border-gray-100 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors {{ $notification->unread() ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}"
                    wire:click="readAndRedirect('{{ $notification->id }}')">
                    <div class="flex items-start gap-3">
                        <!-- Icon -->
                        <div class="flex-shrink-0">
                            <div
                                class="w-8 h-8 {{ $this->getNotificationColor($notification->data['color'] ?? 'blue') }} rounded-md flex items-center justify-center">
                                <flux:icon
                                    name="{{ $this->getNotificationIcon($notification->data['icon'] ?? 'bell') }}"
                                    class="w-4 h-4 text-white" />
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 dark:text-white">
                                {{ $notification->data['message'] }}
                            </p>
                            @if (isset($notification->data['occupant_name']))
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    Occupant: {{ $notification->data['occupant_name'] }}
                                </p>
                            @endif
                            @if (isset($notification->data['invoice_number']))
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    Invoice: {{ $notification->data['invoice_number'] }}
                                    @if (isset($notification->data['amount']))
                                        - Rp {{ number_format($notification->data['amount'], 0, ',', '.') }}
                                    @endif
                                </p>
                            @endif
                            @if (isset($notification->data['contract_code']))
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    Contract: {{ $notification->data['contract_code'] }}
                                </p>
                            @endif
                            <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                {{ $notification->created_at->diffForHumans() }}
                            </p>
                        </div>

                        <!-- Unread indicator -->
                        @if ($notification->unread())
                            <div class="flex-shrink-0">
                                <div class="w-2 h-2 bg-blue-600 rounded-full"></div>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="px-4 py-6 text-center">
                    <flux:icon.bell class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-2" />
                    <p class="text-sm text-gray-500 dark:text-gray-400">No notifications yet</p>
                </div>
            @endforelse
        </div>

        <!-- Footer -->
        @if ($notifications->count() > 0)
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('managers.notifications') }}"
                    class="block text-center text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                    View all notifications
                </a>
            </div>
        @endif
    </div>
</div>

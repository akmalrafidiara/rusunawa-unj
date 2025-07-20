<div class="flex flex-col gap-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">Notifikasi</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">Kelola notifikasi dan peringatan sistem</p>
        </div>

        <!-- Action Buttons -->
        <div class="flex items-center gap-3">
            @if (!empty($selectedNotifications))
                <flux:button 
                    wire:click="markSelectedAsRead"
                    variant="primary"
                    size="sm"
                    icon="check"
                >
                    Tandai Dipilih Sebagai Dibaca
                </flux:button>
                <flux:button 
                    wire:click="deleteSelected"
                    variant="danger"
                    size="sm"
                    icon="trash"
                >
                    Hapus Dipilih
                </flux:button>
            @endif
            <flux:button 
                wire:click="markAllAsRead"
                variant="outline"
                size="sm"
                icon="check-circle"
            >
                Tandai Semua Dibaca
            </flux:button>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="flex items-center gap-2 p-1 bg-zinc-100 dark:bg-zinc-800 rounded-lg w-fit">
        <flux:button
            wire:click="$set('filter', 'all')"
            variant="{{ $filter === 'all' ? 'primary' : 'ghost' }}"
            size="sm"
            class="px-4"
        >
            Semua
            @if($filter === 'all')
                <flux:badge size="sm" color="white" class="ml-2">{{ $notifications->total() ?? 0 }}</flux:badge>
            @endif
        </flux:button>
        <flux:button
            wire:click="$set('filter', 'unread')"
            variant="{{ $filter === 'unread' ? 'primary' : 'ghost' }}"
            size="sm"
            class="px-4"
        >
            Belum Dibaca
            @if($filter === 'unread' && $unreadCount > 0)
                <flux:badge size="sm" color="red" class="ml-2">{{ $unreadCount }}</flux:badge>
            @endif
        </flux:button>
        <flux:button
            wire:click="$set('filter', 'read')"
            variant="{{ $filter === 'read' ? 'primary' : 'ghost' }}"
            size="sm"
            class="px-4"
        >
            Sudah Dibaca
        </flux:button>
    </div>

    <!-- Notifications List -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        @if ($notifications->count() > 0)
            <!-- Select All Header -->
            <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
                <label class="flex items-center">
                    <input type="checkbox" wire:model.live="selectAll" wire:click="toggleSelectAll"
                        class="rounded border-gray-300 text-blue-600 focus:border-blue-500 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">
                        Select all ({{ $notifications->count() }} notifications)
                    </span>
                </label>
            </div>

            <!-- Notifications -->
            @foreach ($notifications as $notification)
                <div
                    class="px-4 py-4 border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors {{ $notification->unread() ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                    <div class="flex items-start gap-4">
                        <!-- Checkbox -->
                        <div class="flex-shrink-0 pt-1">
                            <input type="checkbox" value="{{ $notification->id }}"
                                wire:model.live="selectedNotifications"
                                class="rounded border-gray-300 text-blue-600 focus:border-blue-500 focus:ring-blue-500">
                        </div>

                        <!-- Icon -->
                        <div class="flex-shrink-0">
                            <div
                                class="w-10 h-10 {{ $this->getNotificationColor($notification->data['color'] ?? 'blue') }} rounded-lg flex items-center justify-center">
                                <flux:icon
                                    name="{{ $this->getNotificationIcon($notification->data['icon'] ?? 'bell') }}"
                                    class="w-5 h-5 text-white" />
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-4">
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">
                                        {{ $notification->data['message'] }}
                                    </p>

                                    <!-- Additional Info -->
                                    <div class="mt-1 space-y-1">
                                        @if (isset($notification->data['occupant_name']))
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                <span class="font-medium">Occupant:</span>
                                                {{ $notification->data['occupant_name'] }}
                                            </p>
                                        @endif
                                        @if (isset($notification->data['invoice_number']))
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                <span class="font-medium">Invoice:</span>
                                                {{ $notification->data['invoice_number'] }}
                                                @if (isset($notification->data['amount']))
                                                    - Rp
                                                    {{ number_format($notification->data['amount'], 0, ',', '.') }}
                                                @endif
                                            </p>
                                        @endif
                                        @if (isset($notification->data['contract_code']))
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                <span class="font-medium">Contract:</span>
                                                {{ $notification->data['contract_code'] }}
                                            </p>
                                        @endif
                                        @if (isset($notification->data['report_id']))
                                            <p class="text-xs text-gray-500 dark:text-gray-400">
                                                <span class="font-medium">Report ID:</span>
                                                #{{ $notification->data['report_id'] }}
                                            </p>
                                        @endif
                                    </div>

                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">
                                        {{ $notification->created_at->format('M d, Y \a\t h:i A') }}
                                        ({{ $notification->created_at->diffForHumans() }})
                                    </p>
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center gap-2">
                                    @if ($notification->unread())
                                        <button wire:click="markAsRead('{{ $notification->id }}')"
                                            class="text-xs text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300">
                                            Mark as read
                                        </button>
                                        <div class="w-2 h-2 bg-blue-600 rounded-full"></div>
                                    @endif

                                    @if (isset($notification->data['url']))
                                        <button
                                            wire:click="readAndRedirect('{{ $notification->id }}', '{{ $notification->data['url'] }}')"
                                            class="text-xs text-gray-600 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300">
                                            View
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Pagination -->
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                {{ $notifications->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="px-4 py-12 text-center">
                <flux:icon.bell class="w-16 h-16 text-gray-300 dark:text-gray-600 mx-auto mb-4" />
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                    @if ($filter === 'unread')
                        No unread notifications
                    @elseif($filter === 'read')
                        No read notifications
                    @else
                        No notifications yet
                    @endif
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    @if ($filter === 'unread')
                        You're all caught up! No new notifications to review.
                    @elseif($filter === 'read')
                        No previously read notifications found.
                    @else
                        When you receive notifications, they'll appear here.
                    @endif
                </p>
            </div>
        @endif
    </div>
</div>

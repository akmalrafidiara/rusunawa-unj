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
                <flux:button wire:click="markSelectedAsRead" variant="primary" size="sm" icon="check">
                    Tandai Dipilih Sebagai Dibaca
                </flux:button>
                <flux:button wire:click="deleteSelected" variant="danger" size="sm" icon="trash">
                    Hapus Dipilih
                </flux:button>
            @endif
            <flux:button wire:click="markAllAsRead" variant="outline" size="sm" icon="check-circle">
                Tandai Semua Dibaca
            </flux:button>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="flex items-center gap-2 p-1 bg-zinc-100 dark:bg-zinc-800 rounded-lg w-fit">
        <flux:button wire:click="$set('filter', 'all')" variant="{{ $filter === 'all' ? 'primary' : 'ghost' }}"
            size="sm" class="px-4">
            Semua
            @if ($filter === 'all')
                <flux:badge size="sm" color="white" class="ml-2">{{ $notifications->total() ?? 0 }}</flux:badge>
            @endif
        </flux:button>
        <flux:button wire:click="$set('filter', 'unread')" variant="{{ $filter === 'unread' ? 'primary' : 'ghost' }}"
            size="sm" class="px-4">
            Belum Dibaca
            @if ($filter === 'unread' && $unreadCount > 0)
                <flux:badge size="sm" color="red" class="ml-2">{{ $unreadCount }}</flux:badge>
            @endif
        </flux:button>
        <flux:button wire:click="$set('filter', 'read')" variant="{{ $filter === 'read' ? 'primary' : 'ghost' }}"
            size="sm" class="px-4">
            Sudah Dibaca
        </flux:button>
    </div>

    <!-- Notifications List -->
    <div
        class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        @if ($notifications->count() > 0)
            <!-- Select All Header -->
            <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-700">
                <label class="flex items-center group cursor-pointer">
                    <input type="checkbox" wire:model.live="selectAll" wire:click="toggleSelectAll"
                        class="rounded border-zinc-300 dark:border-zinc-600 text-blue-600 focus:border-blue-500 focus:ring-blue-500 focus:ring-offset-0">
                    <span
                        class="ml-3 text-sm font-medium text-zinc-700 dark:text-zinc-300 group-hover:text-zinc-900 dark:group-hover:text-zinc-100 transition-colors">
                        Pilih semua ({{ $notifications->count() }} notifikasi)
                    </span>
                </label>
            </div>

            <!-- Notifications -->
            @foreach ($notifications as $notification)
                <div
                    class="group border-b border-zinc-200 dark:border-zinc-700 hover:bg-zinc-50 dark:hover:bg-zinc-700/50 transition-colors duration-150 {{ $notification->unread() ? 'bg-blue-50/50 dark:bg-blue-900/10' : '' }}">
                    <div class="px-6 py-5">
                        <div class="flex items-start gap-4">
                            <!-- Checkbox -->
                            <div class="flex-shrink-0 pt-1">
                                <input type="checkbox" value="{{ $notification->id }}"
                                    wire:model.live="selectedNotifications"
                                    class="rounded border-zinc-300 dark:border-zinc-600 text-blue-600 focus:border-blue-500 focus:ring-blue-500 focus:ring-offset-0">
                            </div>

                            <!-- Icon -->
                            <div class="flex-shrink-0">
                                <div
                                    class="w-10 h-10 {{ $this->getNotificationColor($notification->data['color'] ?? 'blue') }} rounded-xl flex items-center justify-center shadow-sm">
                                    <flux:icon
                                        name="{{ $this->getNotificationIcon($notification->data['icon'] ?? 'bell') }}"
                                        class="w-5 h-5 text-white" />
                                </div>
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex-1">
                                        <p
                                            class="text-sm {{ $notification->unread() ? 'font-semibold text-zinc-900 dark:text-zinc-100' : 'font-medium text-zinc-700 dark:text-zinc-300' }}">
                                            {{ $notification->data['message'] }}
                                        </p>

                                        <!-- Additional Info -->
                                        @if (isset($notification->data['occupant_name']) ||
                                                isset($notification->data['invoice_number']) ||
                                                isset($notification->data['contract_code']) ||
                                                isset($notification->data['report_id']))
                                            <div class="mt-2 space-y-1">
                                                @if (isset($notification->data['occupant_name']))
                                                    <p
                                                        class="text-xs text-zinc-600 dark:text-zinc-400 flex items-center gap-1">
                                                        <flux:icon.user class="w-3 h-3" />
                                                        <span class="font-medium">Penghuni:</span>
                                                        {{ $notification->data['occupant_name'] }}
                                                    </p>
                                                @endif
                                                @if (isset($notification->data['invoice_number']))
                                                    <p
                                                        class="text-xs text-zinc-600 dark:text-zinc-400 flex items-center gap-1">
                                                        <flux:icon.document-text class="w-3 h-3" />
                                                        <span class="font-medium">Invoice:</span>
                                                        {{ $notification->data['invoice_number'] }}
                                                        @if (isset($notification->data['amount']))
                                                            - <span
                                                                class="font-semibold text-green-600 dark:text-green-400">Rp
                                                                {{ number_format($notification->data['amount'], 0, ',', '.') }}</span>
                                                        @endif
                                                    </p>
                                                @endif
                                                @if (isset($notification->data['contract_code']))
                                                    <p
                                                        class="text-xs text-zinc-600 dark:text-zinc-400 flex items-center gap-1">
                                                        <flux:icon.document class="w-3 h-3" />
                                                        <span class="font-medium">Kontrak:</span>
                                                        {{ $notification->data['contract_code'] }}
                                                    </p>
                                                @endif
                                                @if (isset($notification->data['report_id']))
                                                    <p
                                                        class="text-xs text-zinc-600 dark:text-zinc-400 flex items-center gap-1">
                                                        <flux:icon.flag class="w-3 h-3" />
                                                        <span class="font-medium">ID Laporan:</span>
                                                        #{{ $notification->data['report_id'] }}
                                                    </p>
                                                @endif
                                            </div>
                                        @endif

                                        <div class="flex items-center gap-3 mt-3">
                                            <p class="text-xs text-zinc-500 dark:text-zinc-400 flex items-center gap-1">
                                                <flux:icon.clock class="w-3 h-3" />
                                                {{ $notification->created_at->format('d M Y, H:i') }}
                                                <span
                                                    class="text-zinc-400 dark:text-zinc-500">({{ $notification->created_at->diffForHumans() }})</span>
                                            </p>
                                        </div>
                                    </div>

                                    <!-- Status & Actions -->
                                    <div class="flex items-center gap-3">
                                        @if ($notification->unread())
                                            <div class="flex items-center gap-2">
                                                <flux:badge color="blue" size="sm">Baru</flux:badge>
                                                <div class="w-2 h-2 bg-blue-500 rounded-full"></div>
                                            </div>
                                        @endif

                                        <div
                                            class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity duration-150">
                                            @if (isset($notification->data['url']))
                                                <flux:button
                                                    wire:click="readAndRedirect('{{ $notification->id }}', '{{ $notification->data['url'] }}')"
                                                    variant="outline" size="xs" icon="arrow-top-right-on-square">
                                                    Lihat
                                                </flux:button>
                                            @endif

                                            @if ($notification->unread())
                                                <flux:button wire:click="markAsRead('{{ $notification->id }}')"
                                                    variant="ghost" size="xs" icon="check">
                                                    Tandai dibaca
                                                </flux:button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Pagination -->
            <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800">
                {{ $notifications->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="px-6 py-16 text-center">
                <div class="w-20 h-20 mx-auto mb-6 text-zinc-300 dark:text-zinc-600">
                    <flux:icon.bell class="w-full h-full" />
                </div>
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100 mb-2">
                    @if ($filter === 'unread')
                        Tidak ada notifikasi yang belum dibaca
                    @elseif($filter === 'read')
                        Tidak ada notifikasi yang sudah dibaca
                    @else
                        Belum ada notifikasi
                    @endif
                </h3>
                <p class="text-sm text-zinc-500 dark:text-zinc-400 max-w-sm mx-auto">
                    @if ($filter === 'unread')
                        Semua notifikasi sudah dibaca! Anda sudah mengetahui semua update terbaru.
                    @elseif($filter === 'read')
                        Belum ada notifikasi yang dibaca sebelumnya.
                    @else
                        Notifikasi terbaru akan muncul di sini ketika ada aktivitas baru di sistem.
                    @endif
                </p>
            </div>
        @endif
    </div>
</div>

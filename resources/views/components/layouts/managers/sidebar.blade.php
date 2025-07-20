<?php
use App\Enums\RoleUser; // Import the RoleUser enum
use Illuminate\Support\Facades\Auth; // Import Auth facade

// Check if the authenticated user has the 'staff_of_rusunawa' role
$isStaffUser = Auth::check() && Auth::user()->hasRole(RoleUser::STAFF_OF_RUSUNAWA->value);

$adminSidebarMenu = [
    [
        'group' => 'Dashboard',
        'items' => [
            [
                'icon' => 'home',
                'label' => __('Overview'),
                'route' => route('dashboard'),
                'current' => request()->routeIs('dashboard'),
                'badge' => null,
            ],
        ],
    ],
    [
        'group' => 'Respon Cepat',
        'items' => [
            [
                'icon' => 'identification',
                'label' => __('Verifikasi Penghuni'),
                'route' => route('occupant.verification'),
                'current' => request()->routeIs('occupant.verification'),
                'badge' => $pendingOccupants ?? 0,
                'roles' => [RoleUser::ADMIN->value, RoleUser::HEAD_OF_RUSUNAWA->value], // Add roles
            ],
            [
                'icon' => 'credit-card',
                'label' => __('Konfirmasi Pembayaran'),
                'route' => route('payment.confirmation'),
                'current' => request()->routeIs('payment.confirmation'),
                'badge' => $pendingPayments ?? 0,
                'roles' => [RoleUser::ADMIN->value, RoleUser::HEAD_OF_RUSUNAWA->value], // Add roles
            ],
        ],
    ],
    [
        'group' => 'Penyewaan',
        'items' => [
            [
                'icon' => 'rectangle-stack',
                'label' => __('Kontrak'),
                'route' => route('contracts'),
                'current' => request()->routeIs('contracts'),
                'badge' => null,
                'roles' => [RoleUser::ADMIN->value, RoleUser::HEAD_OF_RUSUNAWA->value], // Add roles
            ],
            [
                'icon' => 'banknotes',
                'label' => __('Tagihan'),
                'route' => route('invoices'),
                'current' => request()->routeIs('invoices'),
                'badge' => null,
                'roles' => [RoleUser::ADMIN->value, RoleUser::HEAD_OF_RUSUNAWA->value, RoleUser::STAFF_OF_RUSUNAWA->value], // Add roles
            ],
            [
                'icon' => 'user-circle',
                'label' => __('Data Penghuni'),
                'route' => route('occupants'),
                'current' => request()->routeIs('occupants'),
                'badge' => null,
                'roles' => [RoleUser::ADMIN->value, RoleUser::HEAD_OF_RUSUNAWA->value, RoleUser::STAFF_OF_RUSUNAWA->value], // Add roles
            ],
        ],
    ],
    [
        'group' => 'Oprasional',
        'items' => [
            [
                'icon' => 'document-chart-bar',
                'label' => __('Laporan Keuangan'),
                'route' => route('income.reports'),
                'current' => request()->routeIs('income.reports'),
                'badge' => null,
                'roles' => [RoleUser::ADMIN->value, RoleUser::HEAD_OF_RUSUNAWA->value], // Add roles
            ],
            [
                'icon' => 'users',
                'label' => __('Manajemen User'),
                'route' => route('users'),
                'current' => request()->routeIs('users'),
                'badge' => null,
                'roles' => [RoleUser::ADMIN->value, RoleUser::HEAD_OF_RUSUNAWA->value], // Add roles
            ],
            [
                'expandable' => true,
                'label' => __('Manajemen Unit'),
                'items' => [
                    [
                        'icon' => 'building-office',
                        'label' => __('Unit'),
                        'route' => route('units'),
                        'current' => request()->routeIs('units'),
                        'roles' => [RoleUser::ADMIN->value, RoleUser::HEAD_OF_RUSUNAWA->value], // Add roles
                    ],
                    [
                        'icon' => 'tag',
                        'label' => __('Tipe Unit'),
                        'route' => route('unit.types'),
                        'current' => request()->routeIs('unit.types'),
                        'roles' => [RoleUser::ADMIN->value, RoleUser::HEAD_OF_RUSUNAWA->value], // Add roles
                    ],
                    [
                        'icon' => 'squares-2x2',
                        'label' => __('Cluster Unit'),
                        'route' => route('unit.clusters'),
                        'current' => request()->routeIs('unit.clusters'),
                        'roles' => [RoleUser::ADMIN->value, RoleUser::HEAD_OF_RUSUNAWA->value], // Add roles
                    ],
                    [
                        'icon' => 'user-group',
                        'label' => __('Tipe Penghuni'),
                        'route' => route('occupant.types'),
                        'current' => request()->routeIs('occupant.types'),
                        'roles' => [RoleUser::ADMIN->value, RoleUser::HEAD_OF_RUSUNAWA->value], // Add roles
                    ],
                ],
            ],
            [
                'icon' => 'flag',
                'label' => __('Laporan & Keluhan'),
                'route' => route('reports.and.complaints'),
                'current' => request()->routeIs('reports.and.complaints'),
                'badge' => $pendingReports ?? 0,
                'roles' => [RoleUser::ADMIN->value, RoleUser::HEAD_OF_RUSUNAWA->value, RoleUser::STAFF_OF_RUSUNAWA->value], // Add roles
            ],
            [
                'icon' => 'wrench-screwdriver',
                'label' => __('Maintenance'),
                'route' => route('maintenance'),
                'current' => request()->routeIs('maintenance'),
                'badge' => null,
                'roles' => [RoleUser::ADMIN->value, RoleUser::HEAD_OF_RUSUNAWA->value], // Add roles
            ],
        ],
    ],
    [
        'group' => 'Manajemen Konten',
        'items' => [
            [
                'expandable' => true,
                'label' => __('Beranda'),
                'items' => [
                    [
                        'icon' => 'window',
                        'label' => __('Banner & Footer'),
                        'route' => route('page-contents.banner-footer'),
                        'current' => request()->routeIs('page-contents.banner-footer'),
                        'roles' => [RoleUser::ADMIN->value, RoleUser::HEAD_OF_RUSUNAWA->value], // Add roles
                    ],
                    [
                        'icon' => 'building-office-2',
                        'label' => __('Tentang Rusunawa'),
                        'route' => route('page-contents.abouts'),
                        'current' => request()->routeIs('page-contents.abouts'),
                        'roles' => [RoleUser::ADMIN->value, RoleUser::HEAD_OF_RUSUNAWA->value], // Add roles
                    ],
                    [
                        'icon' => 'map-pin',
                        'label' => __('Lokasi Rusunawa'),
                        'route' => route('page-contents.locations'),
                        'current' => request()->routeIs('page-contents.locations'),
                        'roles' => [RoleUser::ADMIN->value, RoleUser::HEAD_OF_RUSUNAWA->value], // Add roles
                    ],
                    [
                        'icon' => 'photo',
                        'label' => __('Galeri'),
                        'route' => route('page-contents.galleries'),
                        'current' => request()->routeIs('page-contents.galleries'),
                        'roles' => [RoleUser::ADMIN->value, RoleUser::HEAD_OF_RUSUNAWA->value], // Add roles
                    ],
                    [
                        'icon' => 'chat-bubble-left',
                        'label' => __('Layanan Pengaduan'),
                        'route' => route('page-contents.complaint-content'),
                        'current' => request()->routeIs('page-contents.complaint-content'),
                        'roles' => [RoleUser::ADMIN->value, RoleUser::HEAD_OF_RUSUNAWA->value], // Add roles
                    ],
                    [
                        'icon' => 'question-mark-circle',
                        'label' => __('FAQ'),
                        'route' => route('page-contents.faq'),
                        'current' => request()->routeIs('page-contents.faq'),
                        'roles' => [RoleUser::ADMIN->value, RoleUser::HEAD_OF_RUSUNAWA->value], // Add roles
                    ],
                    [
                        'icon' => 'phone-arrow-up-right',
                        'label' => __('Kontak Kami'),
                        'route' => route('page-contents.contacts'),
                        'current' => request()->routeIs('page-contents.contacts'),
                        'roles' => [RoleUser::ADMIN->value, RoleUser::HEAD_OF_RUSUNAWA->value], // Add roles
                    ],
                ],
            ],
            [
                'icon' => 'chat-bubble-bottom-center-text',
                'label' => __('Pertanyaan Pengunjung'),
                'route' => route('guest.questions'),
                'current' => request()->routeIs('guest.questions'),
                'badge' => $pendingQuestions ?? 0,
                'roles' => [RoleUser::ADMIN->value, RoleUser::HEAD_OF_RUSUNAWA->value], // Add roles
            ],
            [
                'icon' => 'megaphone',
                'label' => __('Pengumuman'),
                'route' => route('announcements'),
                'current' => request()->routeIs('announcements'),
                'badge' => null,
                'roles' => [RoleUser::ADMIN->value, RoleUser::HEAD_OF_RUSUNAWA->value], // Add roles
            ],
            [
                'icon' => 'document-text',
                'label' => __('Tata Tertib'),
                'route' => route('regulations'),
                'current' => request()->routeIs('regulations'),
                'badge' => null,
                'roles' => [RoleUser::ADMIN->value, RoleUser::HEAD_OF_RUSUNAWA->value], // Add roles
            ],
            [
                'icon' => 'phone',
                'label' => __('Kontak Darurat'),
                'route' => route('emergency.contacts'),
                'current' => request()->routeIs('emergency.contacts'),
                'badge' => null,
                'roles' => [RoleUser::ADMIN->value, RoleUser::HEAD_OF_RUSUNAWA->value], // Add roles
            ],
        ],
    ],
];

// Filter the menu items based on the user's role
$filteredAdminSidebarMenu = [];
foreach ($adminSidebarMenu as $group) {
    $filteredItems = [];
    foreach ($group['items'] as $item) {
        $allowed = false;
        // If 'roles' key is not set, it means it's accessible to everyone (including staff by default)
        // Or if it's set, check if the current user has any of the allowed roles for this item
        if (!isset($item['roles'])) {
            $allowed = true;
        } elseif (Auth::check()) {
            foreach ($item['roles'] as $role) {
                if (Auth::user()->hasRole($role)) {
                    $allowed = true;
                    break;
                }
            }
        }

        if ($allowed) {
            // For expandable items, recursively filter their sub-items
            if (isset($item['expandable']) && $item['expandable'] && isset($item['items'])) {
                $filteredSubItems = [];
                foreach ($item['items'] as $subItem) {
                    $subAllowed = false;
                    if (!isset($subItem['roles'])) {
                        $subAllowed = true;
                    } elseif (Auth::check()) {
                        foreach ($subItem['roles'] as $role) {
                            if (Auth::user()->hasRole($role)) {
                                $subAllowed = true;
                                break;
                            }
                        }
                    }
                    if ($subAllowed) {
                        $filteredSubItems[] = $subItem;
                    }
                }
                // Only add expandable item if it has visible sub-items
                if (!empty($filteredSubItems)) {
                    $item['items'] = $filteredSubItems;
                    $filteredItems[] = $item;
                }
            } else {
                $filteredItems[] = $item;
            }
        }
    }
    // Only add a group if it has visible items
    if (!empty($filteredItems)) {
        $group['items'] = $filteredItems;
        $filteredAdminSidebarMenu[] = $group;
    }
}
?>

<flux:sidebar sticky stashable class="border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900 w-full">
    <flux:sidebar.toggle class="lg:hidden" icon="x-mark" />

    <a href="{{ route('dashboard') }}" class="me-5 flex items-center space-x-2 rtl:space-x-reverse" wire:navigate.hover>
        <x-default.app-logo />
    </a>

    {{-- Dynamic Sidebar Menu --}}
    <flux:navlist variant="outline" class="space-y-4">
        @foreach ($filteredAdminSidebarMenu as $menu)
            <flux:navlist.group :heading="__($menu['group'])" class="grid">
                @foreach ($menu['items'] as $item)
                    @if (isset($item['expandable']) && $item['expandable'])
                        {{-- Determine if the expandable group should be expanded --}}
                        @php
                            $isExpanded = false;
                            foreach ($item['items'] as $subItem) {
                                if ($subItem['current']) {
                                    $isExpanded = true;
                                    break;
                                }
                            }
                        @endphp
                        <flux:navlist.group :heading="$item['label']" expandable :expanded="$isExpanded"
                            :icon="isset($item['icon']) ? $item['icon'] : null">
                            @foreach ($item['items'] as $subItem)
                                <flux:navlist.item :href="$subItem['route']" :current="$subItem['current']"
                                    wire:navigate.hover :icon="isset($subItem['icon']) ? $subItem['icon'] : null">
                                    {{ $subItem['label'] }}
                                </flux:navlist.item>
                            @endforeach
                        </flux:navlist.group>
                    @else
                        <flux:navlist.item :icon="$item['icon']" :href="$item['route']" :current="$item['current']"
                            wire:navigate.hover :badge="$item['badge']" badge-color="red">
                            {{ $item['label'] }}
                        </flux:navlist.item>
                    @endif
                @endforeach
            </flux:navlist.group>
        @endforeach
    </flux:navlist>

    <flux:spacer />

    <flux:dropdown position="bottom" align="start">
        {{-- blade-formatter-disable --}}
        <flux:profile :name="auth()->user()->name" :initials="auth()->user()->initials()"
            icon-trailing="chevrons-up-down" />
        {{-- blade-formatter-enable --}}
        <flux:menu class="w-[220px]">
            <flux:menu.radio.group>
                <div class="p-0 text-sm font-normal">
                    <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                        <span class="relative flex h-8 w-8 shrink-0 overflow-hidden rounded-lg">
                            <span
                                class="flex h-full w-full items-center justify-center rounded-lg bg-neutral-200 text-black dark:bg-neutral-700 dark:text-white">
                                {{ auth()->user()->initials() }}
                            </span>
                        </span>

                        <div class="grid flex-1 text-start text-sm leading-tight">
                            <span class="truncate font-semibold">{{ auth()->user()->name }}</span>
                            <span class="truncate text-xs">{{ auth()->user()->email }}</span>
                        </div>
                    </div>
                </div>
            </flux:menu.radio.group>

            <flux:menu.separator />

            <flux:menu.radio.group>
                <flux:menu.item :href="route('home')" icon="home" wire:navigate.hover>
                    {{ __('Home') }}
                </flux:menu.item>
            </flux:menu.radio.group>

            <flux:menu.separator />

            <flux:menu.radio.group>
                <flux:menu.item :href="route('managers.settings.profile')" icon="cog" wire:navigate.hover>
                    {{ __('Pengaturan Akun') }}
                </flux:menu.item>
            </flux:menu.radio.group>

            <flux:menu.separator />

            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle" class="w-full">
                    {{ __('Keluar') }}
                </flux:menu.item>
            </form>
        </flux:menu>
    </flux:dropdown>
</flux:sidebar>

@php
    $tab = $tab ?? 'users';
    $body = $body ?? 'user-management';
@endphp

<x-layouts.app :title="__('User Management')">
    <div class="flex flex-col gap-6">
        <!-- Judul -->
        <x-managers.ui.page-title title="Manajemen Pengguna" subtitle="Kelola data pengguna sistem Rusunawa UNJ" />

        <livewire:managers.user-management />
    </div>
</x-layouts.app>

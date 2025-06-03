<x-layouts.app.sidebar :title="$title ?? null">
    <flux:main>
        {{ $slot }}
    </flux:main>
</x-layouts.app.sidebar>

<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@stack('scripts')

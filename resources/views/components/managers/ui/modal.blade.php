@if ($show)
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-6">
        <div
            class="bg-white dark:bg-zinc-900 rounded-lg shadow-lg w-full mx-auto p-6 max-h-full overflow-y-auto {{ $class ?? '' }}">
            <h2 class="text-xl font-semibold mb-4">{{ $title }}</h2>
            {{ $slot }}
        </div>
    </div>
@endif

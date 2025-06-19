@props(['show' => false, 'title' => '', 'class' => ''])

@if ($show)
    <div x-data="{ isOpen: false }" x-init="$nextTick(() => isOpen = true)"
        @close.window="isOpen = false; $nextTick(() => $dispatch('modal-closed'))" x-show="isOpen" x-cloak
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-6"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100">

        <div class="bg-white dark:bg-zinc-900 rounded-lg shadow-lg w-full mx-auto p-6 max-h-full overflow-y-auto {{ $class }}"
            x-show="isOpen" x-transition:enter="transition ease-out duration-200 transform"
            x-transition:enter-start="opacity-0 scale-90 translate-y-4"
            x-transition:enter-end="opacity-100 scale-100 translate-y-0">

            <h2 class="text-xl font-semibold mb-4">{{ $title }}</h2>
            {{ $slot }}
        </div>
    </div>
@endif

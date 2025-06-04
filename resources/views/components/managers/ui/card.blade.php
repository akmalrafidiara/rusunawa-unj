<div class="bg-white dark:bg-zinc-900 rounded-lg shadow border dark:border-zinc-700 overflow-hidden">
    <div {{ $attributes->merge(['class' => 'p-4']) }}>
        {{ $slot }}
    </div>
</div>

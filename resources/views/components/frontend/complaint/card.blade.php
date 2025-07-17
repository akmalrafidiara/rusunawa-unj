<div class="bg-gray-40 dark:bg-zinc-700 rounded-lg border dark:border-zinc-700 overflow-hidden">
    <div {{ $attributes->class([
        'p-4' => !str($attributes->get('class'))->contains('p-'),
    ]) }}>
        {{ $slot }}
    </div>
</div>
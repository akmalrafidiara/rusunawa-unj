<div class="bg-white dark:bg-zinc-800 rounded-lg shadow border dark:border-zinc-700">
    <div {{ $attributes->class([
        'p-4' => !str($attributes->get('class'))->contains('p-'),
    ]) }}>
        {{ $slot }}
    </div>
</div>
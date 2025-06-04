<div class="bg-white dark:bg-zinc-800 rounded-lg shadow border dark:border-zinc-700 overflow-hidden">
    <div {{ $attributes->class([
        'p-4' => !str($attributes->get('class'))->contains('p-'),
    ]) }}>
        {{ $slot }}
    </div>
</div>


{{-- <div class="bg-white dark:bg-zinc-900 rounded-lg shadow border dark:border-zinc-700 overflow-hidden">
    <div {{ $attributes->merge([
        'class' => collect([
            'text-sm', // <-- class Wajib, tidak bisa diubah
        ])->when(
            ! str($attributes->get('class'))->contains('p-'),
            fn ($classes) => $classes->push('p-4') // <-- padding default kalau tidak diset user
        )->implode(' ')
    ]) }}>
        {{ $slot }}
    </div>
</div> --}}

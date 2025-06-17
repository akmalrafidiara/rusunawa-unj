{{-- resources/views/components/managers/form/checkbox.blade.php --}}

@props([
    'label' => '',
    'description' => '',
])

@php
    $wireModel = $attributes->whereStartsWith('wire:model')->first();
    $id = $attributes->get('id', $wireModel);
    $error = $wireModel && $errors->has($wireModel);

    // DITAMBAHKAN: Ambil nilai awal (true/false) dari properti Livewire.
    // data_get($__livewire) adalah cara aman untuk mengakses properti komponen Livewire.
    $value = $wireModel ? data_get($__livewire, $wireModel) : null;
@endphp

<div class="w-full">
    <div class="relative flex items-start">
        <div class="flex h-6 items-center">
            <input id="{{ $id }}" type="checkbox" {{ $attributes->whereStartsWith('wire:model') }}
                {{-- DITAMBAHKAN: Pintasan Blade untuk menambahkan atribut 'checked' jika $value adalah true. --}} @checked($value)
                class="h-4 w-4 rounded border-gray-400 dark:border-gray-600 bg-transparent dark:bg-transparent text-blue-600 focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-blue-600">
        </div>
        <div class="ml-3 text-sm leading-6">
            <label for="{{ $id }}" class="font-medium text-gray-900 dark:text-gray-100 cursor-pointer">
                {{ $label }}
            </label>
            @if ($description)
                <p class="text-gray-500 dark:text-gray-400">{{ $description }}</p>
            @endif
        </div>
    </div>

    @if ($error)
        <p class="mt-2 text-sm text-red-600">{{ $errors->first($wireModel) }}</p>
    @endif
</div>

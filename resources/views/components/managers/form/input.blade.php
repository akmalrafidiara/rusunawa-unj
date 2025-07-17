@props([
    'type' => 'text',
    'placeholder' => 'Masukkan nilai...',
    'icon' => null,
    'class' => 'w-full',
    'required' => false,
    'clearable' => false,
    'rupiah' => false,
])

@php
    $wireModel = $attributes->whereStartsWith('wire:model')->first();
    $error = $wireModel && $errors->has($wireModel);
    $value = $wireModel ? data_get($__livewire ?? null, $wireModel) : null;
@endphp

<div class="w-full">
    {{-- Container utama untuk input dan ikon --}}
    <div class="relative">

        {{-- Ikon di sebelah kiri (jika ada) --}}
        @if ($icon)
            <div class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none">
                <flux:icon :name="$icon" class="h-5 w-5 text-gray-400 dark:text-gray-300" />
            </div>
        @endif

        {{-- Logika untuk Input Rupiah --}}
        @if ($rupiah)
            <div x-data="{
                raw: $wire.entangle('{{ $wireModel }}'),
                display: '',
                format() {
                    if (this.raw === null || this.raw === '' || isNaN(this.raw)) {
                        this.display = '';
                        return;
                    }
                    this.display = new Intl.NumberFormat('id-ID').format(this.raw);
                },
                parse(value) {
                    const cleanValue = String(value).replace(/[^0-9]/g, '');
                    this.raw = cleanValue === '' ? null : parseInt(cleanValue, 10);
                }
            }" x-init="format();
            $watch('raw', () => format());" class="relative">

                {{-- Simbol "Rp" --}}
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-400">Rp</span>

                {{-- Input yang dilihat pengguna --}}
                <input type="text" x-model="display" x-on:input.debounce.500ms="parse($event.target.value)"
                    placeholder="{{ $placeholder }}" {{ $required ? 'required' : '' }} {{-- Style yang disamakan --}}
                    class="block w-full rounded-md border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:bg-zinc-800 dark:border-gray-600 dark:text-white dark:placeholder-zinc-500 py-2 pl-10 pr-{{ $clearable ? '10' : '4' }} {{ $class }} {{ $error ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-300' }}" />
            </div>

            {{-- Logika untuk Input Biasa (Non-Rupiah) --}}
        @else
            <input type="{{ $type }}" {{ $attributes->wire('model') }} placeholder="{{ $placeholder }}"
                {{ $required ? 'required' : '' }} {{-- Style yang disamakan --}}
                class="block w-full rounded-md shadow-sm focus:border-emerald-500 focus:ring-emerald-500 dark:bg-zinc-800 dark:border-gray-600 dark:text-white dark:placeholder-zinc-500 py-2 {{ $icon ? 'pl-10' : 'pl-4' }} pr-{{ $clearable ? '10' : '4' }} {{ $class }} {{ $error ? 'border-red-500 focus:border-red-500 focus:ring-red-500' : 'border-gray-300' }}" />
        @endif

        {{-- Tombol "Clear" (jika diaktifkan) --}}
        @if ($clearable && $wireModel && $value)
            <button type="button" wire:click="$set('{{ $wireModel }}', null)"
                class="absolute right-3 top-1/2 -translate-y-1/2 cursor-pointer text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none z-10">
                {{-- Gunakan ikon silang yang lebih modern --}}
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                        clip-rule="evenodd" />
                </svg>
            </button>
        @endif
    </div>

    {{-- Tampilan Error --}}
    @if ($error)
        <span class="mt-1 text-sm text-red-600">{{ $errors->first($wireModel) }}</span>
    @endif
</div>

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

    // THIS IS NOT A BUG, this is to get the actual wire:model value
    $value = $wireModel ? data_get($__livewire ?? null, $wireModel) : null;
@endphp

<div class="w-full">
    <div class="relative">
        @if ($icon)
            <div class="absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none">
                <flux:icon :name="$icon" class="h-5 w-5 text-gray-400 dark:text-gray-200" />
            </div>
        @endif

        @if ($rupiah)
            <div x-data="{
                // Hubungkan 'raw' dengan properti Livewire. Ini adalah nilai integer asli (e.g., 1500000)
                raw: $wire.entangle('{{ $wireModel }}'),
            
                // 'display' adalah apa yang dilihat dan diketik oleh pengguna (e.g., '1.500.000')
                display: '',
            
                // Fungsi untuk memformat angka integer menjadi string Rupiah
                format() {
                    // Jangan format jika nilainya kosong atau bukan angka
                    if (this.raw === null || this.raw === '' || isNaN(this.raw)) {
                        this.display = '';
                        return;
                    }
                    this.display = new Intl.NumberFormat('id-ID').format(this.raw);
                },
            
                // Fungsi untuk mengubah string Rupiah kembali menjadi integer
                parse(value) {
                    // Hapus semua karakter non-digit
                    const cleanValue = String(value).replace(/[^0-9]/g, '');
                    this.raw = cleanValue === '' ? null : parseInt(cleanValue, 10);
                }
            }" x-init="// Saat pertama kali dimuat, langsung format nilai yang ada
            format();
            
            // PERBAIKAN UTAMA: Amati perubahan pada 'raw'.
            // Jika nilai dari Livewire masuk, jalankan format() secara otomatis.
            $watch('raw', () => format());" class="relative">

                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 dark:text-gray-300">Rp</span>

                {{-- Input ini sekarang terhubung dengan 'display' dan memanggil 'parse' saat diketik --}}
                <input type="text" x-model="display" x-on:input.live="parse($event.target.value)"
                    placeholder="{{ $placeholder }}" {{ $required ? 'required' : '' }}
                    class="block w-full border rounded-md {{ $error ? 'border-red-500' : 'border-gray-500' }} dark:placeholder-zinc-500 bg-transparent focus:outline-none focus:ring-2 focus:ring-blue-500 py-2 pl-10 pr-{{ $clearable ? '10' : '4' }} {{ $class }}" />
            </div>
        @else
            <input type="{{ $type }}" {{ $attributes->merge(['wire:model' => $wireModel]) }}
                placeholder="{{ $placeholder }}" {{ $required ? 'required' : '' }}
                class="block w-full border rounded-md {{ $error ? 'border-red-500' : 'border-gray-500' }} dark:placeholder-zinc-500 bg-transparent focus:outline-none focus:ring-2 focus:ring-blue-500 py-2 {{ $icon ? 'pl-10' : 'pl-4' }} pr-{{ $clearable ? '10' : '4' }} {{ $class }}" />
        @endif

        @if ($clearable && $wireModel && $value)
            <button type="button" wire:click="$set('{{ $wireModel }}', null)"
                class="absolute right-3 top-1/2 -translate-y-1/2 cursor-pointer text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none z-10">
                <flux:icon name="x-mark" />
            </button>
        @endif
    </div>

    @if ($error)
        <span class="mt-1 text-sm text-red-600">{{ $errors->first($wireModel) }}</span>
    @endif
</div>

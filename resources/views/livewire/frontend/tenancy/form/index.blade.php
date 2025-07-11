@php
    // Definisikan setiap langkah dalam sebuah array agar mudah dikelola
    $steps = [
        1 => ['title' => 'Detail Pemesanan', 'message' => 'Lengkapi detail pemesanan Anda'],
        2 => ['title' => 'Identitas Penghuni', 'message' => 'Isi data diri penghuni dengan lengkap'],
        3 => ['title' => 'Konfirmasi Pemesanan', 'message' => 'Periksa kembali detail pemesanan Anda'],
        4 => ['title' => 'Pemesanan Berhasil', 'message' => 'Formulir pemesanan berhasil dikirim'],
    ];

    if (!session()->has('tenancy_data')) {
        redirect()->back();
    }
@endphp

<div class="container mx-auto p-4 sm:p-6 mb-20">
    <nav aria-label="breadcrumb" class="mb-4 sm:mb-6">
        <ol class="flex items-center space-x-2 text-sm text-gray-600 dark:text-gray-400">
            <li>
                <a href="{{ route('home') }}"
                    class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">
                    Home
                </a>
            </li>
            <li class="flex items-center">
                <flux:icon name="chevron-right" class="w-4 h-4 mx-2 text-gray-400 dark:text-gray-500" />
                <a href="{{ $filterUrl }}"
                    class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">
                    Tenancy
                </a>
            </li>
            <li class="flex items-center">
                <flux:icon name="chevron-right" class="w-4 h-4 mx-2 text-gray-400 dark:text-gray-500" />

                <a href="{{ $detailUrl }}"
                    class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">
                    {{ $unitType->name ?? 'Unit Detail' }}
                </a>
            </li>
            <li class="flex items-center">
                <flux:icon name="chevron-right" class="w-4 h-4 mx-2 text-gray-400 dark:text-gray-500" />
                <span class="text-gray-900 dark:text-gray-100 font-medium">
                    Form
                </span>
            </li>
        </ol>
    </nav>

    <div class="container mx-auto mb-6">
        <h1 class="text-3xl font-bold">Form Pemesanan Kamar</h1>
        @if ($currentStep !== 4)
            <p class="text-gray-500">Langkah {{ $currentStep ?? 1 }} dari 3 -
                {{ $steps[$currentStep ?? 1]['message'] }}</p>
        @else
            <p class="text-gray-500">Pengisian form selesai</p>
        @endif
    </div>

    <div class="container mx-auto flex flex-col-reverse md:flex-row gap-8">
        {{-- Kolom Kiri: Stepper / Progress Bar --}}
        <div class="md:w-1/4">
            @include('livewire.frontend.tenancy.form.stepper')
        </div>

        {{-- Kolom Kanan: Konten Form Dinamis --}}
        <div class="md:w-3/4">
            <div class="bg-white dark:bg-zinc-800 px-0 md:px-6">
                @if ($currentStep === 1)
                    @include('livewire.frontend.tenancy.form.partials.step-1-detail')
                @elseif ($currentStep === 2)
                    @include('livewire.frontend.tenancy.form.partials.step-2-identity')
                @elseif ($currentStep === 3)
                    @include('livewire.frontend.tenancy.form.partials.step-3-confirmation')
                @elseif ($currentStep === 4)
                    @include('livewire.frontend.tenancy.form.partials.step-4-success    ')
                @endif
            </div>
        </div>
    </div>
</div>

<?php

use Livewire\Volt\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Title;

new #[Layout('components.layouts.frontend'), Title('Rusunawa UNJ | Riwayat Pengaduan')] class extends Component
{
    public function with(): array
    {
        return [
            'isLoggedIn' => Auth::check(),
        ];
    }
}; ?>

<section class="w-full">
    @include('modules.frontend.complaint.complaint-heading')
    <div class="container mx-auto relative overflow-hidden -mt-32 md:-mt-25 lg:-mt-25">
        <x-frontend.complaint.layout>
            <h1 class="text-3xl font-bold mb-8 hidden md:block dark:text-gray-100">Riwayat Pengaduan</h1>

            @if ($isLoggedIn)
            {{-- Buat Riwayat Pengaduan --}}
            @else
            {{-- Tampilan Belum Login --}}
            <div class="flex flex-col items-center justify-center min-h-[400px] p-8 text-center">
                <img src="{{ asset('images/dummy-pengaduan-kosong.png') }}" alt="Pengaduan Tidak Tersedia" class="w-60 h-60 object-contain mx-auto">
                <h2 class="text-xl lg:text-2xl font-semibold text-gray-800 mb-2 dark:text-gray-100">Riwayat Pengaduan Tidak Tersedia</h2>
                <p class="text-m lg:text-lg text-gray-600 mb-6 dark:text-gray-300">Mohon Login terlebih dahulu untuk melihat riwayat pengaduan</p>
                <a href="{{ route('login') }}" class="bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-2 px-6 rounded-full text-lg shadow-md transition duration-300 ease-in-out">
                    Login
                </a>
            </div>
            @endif
        </x-frontend.complaint.layout>
    </div>
</section>
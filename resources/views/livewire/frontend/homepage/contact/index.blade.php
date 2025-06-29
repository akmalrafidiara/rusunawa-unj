<div class="container mx-auto px-4 py-8">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Kolom Kiri: Informasi Kontak (memanggil komponen Livewire Volt) --}}
        {{-- Menambahkan padding vertikal (py-4) untuk memberi jarak di dalam kolom kiri --}}
        <div class="md:py-4"> 
            <div class="md:mr-15"> @livewire('frontend.homepage.contact.partials._contact-content')
            </div>
        </div>

        {{-- Kolom Kanan: Panggil Komponen Livewire Form --}}
        <div class="md:py-4">
            @livewire('frontend.homepage.contact.partials._guest-question-form')
        </div>
    </div>
</div>
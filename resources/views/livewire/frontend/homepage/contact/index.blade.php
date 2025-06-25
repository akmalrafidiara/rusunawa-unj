<div class="p-8">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        {{-- Kolom Kiri: Informasi Kontak (memanggil komponen Livewire Volt) --}}
        <div class="py-4">
            @livewire('frontend.homepage.contact.partials._contact-content')
        </div>

        {{-- Kolom Kanan: Panggil Komponen Livewire Form --}}
        <div class="py-4">
            @livewire('frontend.homepage.contact.partials._guest-question-form')
        </div>
    </div>
</div>
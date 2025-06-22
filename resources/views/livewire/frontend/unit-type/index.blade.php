<div class="container mx-auto px-4 py-8">
    {{-- Section Title and "Rent Now" Button --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-10">
        <div>
            <span class="text-sm font-semibold text-green-600 uppercase tracking-wider">Tipe Kamar</span>
            <h2 class="text-4xl font-extrabold text-gray-900 mt-2">Pilihan Jenis Kamar Untukmu</h2>
        </div>
        {{-- Main "Sewa Sekarang" button, redirects to the rental page --}}
        <a href="/tenancy" class="mt-4 md:mt-0 text-green-600 hover:text-green-800 font-semibold flex items-center group">
            Sewa Sekarang
            <svg class="w-4 h-4 ml-2 transform group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
            </svg>
        </a>
    </div>

    {{-- Render the Unit Type Listing component --}}
    @livewire('frontend.unit-type.partials._data-listing')

    {{-- Render the Unit Detail Modal component --}}
    @livewire('frontend.unit-type.partials._modal-detail')
</div>

{{-- JavaScript to manage body scroll behavior when modals are open/closed --}}
@script
<script>
    Livewire.on('lock-body-scroll', () => {
        document.body.classList.add('overflow-hidden');
    });

    Livewire.on('unlock-body-scroll', () => {
        document.body.classList.remove('overflow-hidden');
    });
</script>
@endscript
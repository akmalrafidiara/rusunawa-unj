<div class="container mx-auto px-4 py-8">
    {{-- Bagian Judul dan Tombol "Sewa Sekarang" --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-10">
        <div>
            <span class="text-sm font-semibold text-green-600 dark:text-green-400 uppercase tracking-wider">Tipe Kamar</span>
            <h2 class="text-4xl font-extrabold text-gray-900 dark:text-white mt-2">Pilihan Jenis Kamar Untukmu</h2>
        </div>
        {{-- Tombol utama "Sewa Sekarang", mengarahkan ke halaman penyewaan --}}
        <a href="/tenancy" class="mt-4 md:mt-0 font-semibold flex items-center group
                                text-green-600 hover:text-green-800 dark:text-green-500 dark:hover:text-green-400
        ">
            Sewa Sekarang
            <flux:icon name="arrow-right" class="w-4 h-4 ml-2 transform group-hover:translate-x-1 transition-transform duration-200" />
        </a>
    </div>

    {{-- Render komponen Daftar Tipe Unit --}}
    @livewire('frontend.homepage.unit-type.partials._data-listing')

    {{-- Render komponen Modal Detail Unit --}}
    @livewire('frontend.homepage.unit-type.partials._modal-detail')
</div>

{{-- JavaScript untuk mengatur perilaku scroll body saat modal dibuka/ditutup --}}
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
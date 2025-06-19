<div class="mt-80">
    {{-- Tampilkan teks ringkasan filter --}}
    <div class="container mx-auto px-6 text-center mb-8">
        @if (!empty($filters['occupantType']))
            <p>Menampilkan tipe kamar untuk penghuni <strong>{{ $filters['occupantType'] }}</strong> dengan sewa
                <strong>{{ str_replace('_', ' ', $filters['pricingBasis']) }}</strong>
            </p>
        @endif
    </div>

    {{-- Daftar hasil pencarian Tipe Unit --}}
    <div class="container mx-auto px-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse ($unitAvailables as $unitAvailable)
                {{-- Ini adalah card untuk setiap TIPE UNIT, sesuaikan dengan desain Anda --}}
                <div class="border rounded-lg overflow-hidden shadow-sm hover:shadow-lg transition">
                    <img src="{{ $unitAvailable->unitTypes->first() && $unitAvailable->unitTypes->first()->attachments && $unitAvailable->unitTypes->first()->attachments->isNotEmpty() ? Storage::url($unitAvailable->unitTypes->first()->attachments->first()->path) : asset('images/placeholder.png') }}"
                        alt="Gambar {{ $unitAvailable->unitTypes->first()->name ?? 'Unit Type' }}"
                        class="w-full h-48 object-cover">
                    <div class="p-4">
                        <h3 class="font-bold text-lg text-gray-800 dark:text-white">
                            {{ $unitAvailable->unitTypes->first()->name ?? 'N/A' }}
                        </h3>
                        <p class="text-sm text-gray-500 mb-2">
                            {{ $unitAvailable->unitTypes->first()->available_units_count ?? 0 }} kamar
                            tersedia
                        </p>
                        <p class="text-gray-600 dark:text-gray-300 text-sm mb-4">
                            {{ Str::limit($unitAvailable->unitTypes->first()->description ?? '', 100) }}</p>
                        <div class="flex justify-between items-center">
                            <span class="text-xl font-semibold text-emerald-600">
                                Rp
                                {{ number_format($unitAvailable->price, 0, ',', '.') }}/{{ $unitAvailable->pricing_basis->label() }}
                            </span>
                            <a href="#"
                                class="bg-emerald-600 text-white px-4 py-2 rounded text-sm font-semibold hover:bg-emerald-700">Lihat
                                Detail</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="lg:col-span-3 text-center py-16">
                    <h3 class="text-xl font-semibold">Tipe Kamar Tidak Ditemukan</h3>
                    <p class="text-gray-500 mt-2">Tidak ada tipe kamar yang tersedia sesuai dengan kriteria Anda.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

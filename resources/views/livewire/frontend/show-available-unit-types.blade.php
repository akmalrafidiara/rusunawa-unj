<div class="mt-92 md:mt-36">
    @if (!empty($occupantType) && !empty($pricingBasis))
        {{-- Tampilkan teks ringkasan filter --}}
        <div class="container mx-auto px-6 mb-8">
            @if (!empty($occupantType) && !empty($pricingBasis))
                <p>Menampilkan kamar untuk penghuni <strong>{{ $occupantType }}</strong>
                    dengan sewa
                    <strong>{{ \App\Enums\PricingBasis::from($pricingBasis)->label() }}</strong>
                    @if (!empty($startDate) && !empty($endDate))
                        , Periode <strong>{{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} -
                            {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}</strong>
                    @endif
            @endif
            </p>
        </div>

        {{-- Daftar hasil pencarian Tipe Unit --}}
        <div class="container mx-auto px-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @forelse ($unitTypes as $unitType)
                    <div class="border rounded-lg overflow-hidden shadow-sm hover:shadow-lg transition">
                        <img src="{{ $unitType->attachments->isNotEmpty() ? Storage::url($unitType->attachments->first()->path) : asset('images/placeholder.png') }}"
                            alt="Gambar {{ $unitType->name }}" class="w-full h-48 object-cover">
                        <div class="p-4">
                            <h3 class="font-bold text-lg text-gray-800 dark:text-white">
                                {{ $unitType->name }}
                                <span class="font-light text-sm text-gray-500"> / Tersedia
                                    {{ $unitType->units()->where('status', 'available')->count() }} unit</span>
                            </h3>
                            <p class="text-gray-600 dark:text-gray-300 text-sm mb-4">
                                {{ Str::limit($unitType->description, 100) }}
                            </p>
                            <div class="flex justify-between items-center">
                                {{-- Ambil harga dari relasi unitPrices yang sudah difilter --}}
                                @if ($priceInfo = $unitType->unitPrices->first())
                                    <span class="text-xl font-semibold text-emerald-600">
                                        Rp {{ number_format($priceInfo->price) }}
                                        @if ($priceInfo->max_price)
                                            - {{ number_format($priceInfo->max_price) }}
                                        @endif
                                        <span class="text-sm font-normal text-gray-500">/
                                            {{ $priceInfo->pricing_basis }}
                                        </span>
                                @endif
                                <a href="#"
                                    class="bg-emerald-600 text-white px-4 py-2 rounded text-sm font-semibold hover:bg-emerald-700">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="lg:col-span-3 text-center py-16">
                        <h3 class="text-xl font-semibold">Tipe Kamar Tidak Ditemukan</h3>
                        <p class="text-gray-500 mt-2">Tidak ada tipe kamar yang tersedia sesuai dengan kriteria Anda.
                        </p>
                    </div>
                @endforelse
            </div>
        </div>
    @endif
</div>

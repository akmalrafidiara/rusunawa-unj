@php
    // --- MENGAMBIL DATA LANGSUNG DARI AUTHENTIKASI ---

    // 1. Dapatkan penghuni yang sedang login
    $occupant = Auth::guard('occupant')->user();

    // 2. Dapatkan kontrak yang paling baru
    // Kita eager load semua relasi agar efisien
    $contract = $occupant
        ->contracts()
        ->with(['unit', 'invoices' => fn($q) => $q->latest()])
        ->latest()
        ->first();

    // Jika pengguna tidak memiliki kontrak, tangani kasusnya
    if (!$contract) {
        // Hentikan eksekusi dan tampilkan pesan, atau redirect.
        // Untuk testing, kita akan buat data dummy agar tidak error.
        $contract = (object) [
            'unit' => (object) ['room_number' => 'N/A'],
            'status' => (object) ['label' => 'Tidak Ada'],
            'start_date' => now(),
            'end_date' => now(),
            'invoices' => collect(),
        ];
        $nextUnpaidInvoice = null;
        $duration = '0 Bulan';
    } else {
        // 3. Cari tagihan selanjutnya yang belum dibayar
        $nextUnpaidInvoice = $contract->invoices->where('status', 'unpaid')->first();

        // 4. Hitung sisa durasi kontrak
        $duration = $contract->end_date->diffForHumans($contract->start_date, true);
    }
@endphp

<!DOCTYPE html>
<html lang="id">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dashboard Penghuni - {{ $occupant->full_name }}</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <style>
            .bg-rusunawa {
                background-color: #059669;
            }

            .hover\:bg-rusunawa-dark:hover {
                background-color: #047857;
            }

            .text-rusunawa {
                color: #059669;
            }
        </style>
    </head>

    <body class="bg-gray-100 font-sans">
        <div class="container mx-auto px-4 py-8">
            <div class="mb-8">
                <div class="flex justify-between items-center">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-800">Dashboard Penghuni</h1>
                        <p class="text-gray-600">Selamat datang kembali, {{ explode(' ', $occupant->full_name)[0] }}!
                        </p>
                    </div>
                    <form method="POST" action="{{ route('occupant.auth.logout') }}">
                        @csrf
                        <button type="submit"
                            class="bg-red-500 hover:bg-red-600 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                            Logout
                        </button>
                    </form>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white text-gray-800 rounded-lg p-6 shadow-md">
                    <div class="flex justify-between items-center">
                        <div>
                            <h5 class="text-lg font-medium text-gray-500">Kamar Anda</h5>
                            <h2 class="text-3xl font-bold text-rusunawa">{{ $contract->unit->room_number }}</h2>
                        </div>
                        <div><i class="fas fa-home text-4xl text-gray-300"></i></div>
                    </div>
                </div>
                <div class="bg-white text-gray-800 rounded-lg p-6 shadow-md">
                    <div class="flex justify-between items-center">
                        <div>
                            <h5 class="text-lg font-medium text-gray-500">Status Kontrak</h5>
                            <h4 class="text-2xl font-bold">{{ $contract->status->label() }}</h4>
                        </div>
                        <div><i class="fas fa-check-circle text-4xl text-gray-300"></i></div>
                    </div>
                </div>
                <div class="bg-white text-gray-800 rounded-lg p-6 shadow-md">
                    <div class="flex justify-between items-center">
                        <div>
                            <h5 class="text-lg font-medium text-gray-500">Tagihan Berikutnya</h5>
                            @if ($nextUnpaidInvoice)
                                <h4 class="text-2xl font-bold">Rp
                                    {{ number_format($nextUnpaidInvoice->amount, 0, ',', '.') }}</h4>
                            @else
                                <h4 class="text-2xl font-bold text-green-500">Lunas</h4>
                            @endif
                        </div>
                        <div><i class="fas fa-money-bill-wave text-4xl text-gray-300"></i></div>
                    </div>
                </div>
                <div class="bg-white text-gray-800 rounded-lg p-6 shadow-md">
                    <div class="flex justify-between items-center">
                        <div>
                            <h5 class="text-lg font-medium text-gray-500">Durasi Sewa</h5>
                            <h4 class="text-2xl font-bold">{{ $duration }}</h4>
                        </div>
                        <div><i class="fas fa-file-contract text-4xl text-gray-300"></i></div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="border-b border-gray-200 pb-4 mb-4">
                            <h5 class="text-xl font-semibold">Informasi Penghuni</h5>
                        </div>
                        <div class="text-center mb-6">
                            <img src="{{ $occupant->profile_photo_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($occupant->full_name) . '&background=059669&color=fff' }}"
                                class="rounded-full h-24 w-24 mx-auto" alt="Profile">
                        </div>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between"><span class="font-semibold text-gray-500">Nama</span><span
                                    class="text-right">{{ $occupant->full_name }}</span></div>
                            <div class="flex justify-between"><span class="font-semibold text-gray-500">No.
                                    HP</span><span class="text-right">{{ $occupant->whatsapp_number }}</span></div>
                            <div class="flex justify-between"><span class="font-semibold text-gray-500">Masa
                                    Tinggal</span><span class="text-right">{{ $contract->start_date->format('d M Y') }}
                                    - {{ $contract->end_date->format('d M Y') }}</span></div>
                            @if ($occupant->is_student)
                                <div class="pt-4 border-t mt-4">
                                    <div class="flex justify-between mt-3"><span
                                            class="font-semibold text-gray-500">NIM</span><span
                                            class="text-right">{{ $occupant->student_id }}</span></div>
                                    <div class="flex justify-between mt-3"><span
                                            class="font-semibold text-gray-500">Fakultas</span><span
                                            class="text-right">{{ $occupant->faculty }}</span></div>
                                    <div class="flex justify-between mt-3"><span
                                            class="font-semibold text-gray-500">Jurusan</span><span
                                            class="text-right">{{ $occupant->study_program }}</span></div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="border-b border-gray-200 pb-4 mb-4">
                            <h5 class="text-xl font-semibold">Riwayat Pembayaran</h5>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b">
                                        <th class="text-left font-semibold text-gray-600 py-3">Deskripsi</th>
                                        <th class="text-left font-semibold text-gray-600 py-3">Jumlah</th>
                                        <th class="text-left font-semibold text-gray-600 py-3">Tanggal Bayar</th>
                                        <th class="text-center font-semibold text-gray-600 py-3">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($contract->invoices as $invoice)
                                        <tr class="border-b last:border-b-0">
                                            <td class="py-3">{{ $invoice->description }}</td>
                                            <td class="py-3">Rp {{ number_format($invoice->amount, 0, ',', '.') }}
                                            </td>
                                            <td class="py-3">
                                                {{ $invoice->paid_at ? $invoice->paid_at->format('d M Y') : '-' }}</td>
                                            <td class="py-3 text-center">
                                                <span
                                                    class="text-xs font-medium px-2.5 py-1 rounded-full {{ is_array($invoice->status->color()) ? implode(' ', $invoice->status->color()) : $invoice->status->color() }}">
                                                    {{ ucfirst($invoice->status->value ?? $invoice->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-gray-500">Belum ada riwayat
                                                pembayaran.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mt-8">
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="border-b border-gray-200 pb-4 mb-6">
                        <h5 class="text-xl font-semibold">Aksi Cepat</h5>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <a href="#"
                            class="bg-blue-500 hover:bg-blue-600 text-white font-medium py-3 px-4 rounded-lg text-center transition duration-200">
                            <i class="fas fa-credit-card mr-2"></i>Bayar Tagihan
                        </a>
                        <a href="#"
                            class="bg-green-500 hover:bg-green-600 text-white font-medium py-3 px-4 rounded-lg text-center transition duration-200">
                            <i class="fas fa-history mr-2"></i>Riwayat Pembayaran
                        </a>
                        <a href="#"
                            class="bg-cyan-500 hover:bg-cyan-600 text-white font-medium py-3 px-4 rounded-lg text-center transition duration-200">
                            <i class="fas fa-user-edit mr-2"></i>Edit Profile
                        </a>
                        <a href="#"
                            class="bg-yellow-500 hover:bg-yellow-600 text-white font-medium py-3 px-4 rounded-lg text-center transition duration-200">
                            <i class="fas fa-exclamation-triangle mr-2"></i>Lapor Masalah
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </body>

</html>

<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Dashboard Penghuni</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    </head>

    <body class="bg-gray-100">
        <div class="container mx-auto px-4 py-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex justify-between items-center">
                    <h1 class="text-3xl font-bold text-gray-800">Dashboard Penghuni</h1>
                    <div class="text-gray-600">
                        <i class="fas fa-calendar-alt mr-2"></i>{{ date('d M Y') }}
                    </div>
                </div>
            </div>

            <!-- User Session Info -->
            <div class="mb-4 text-sm text-gray-600">
                <p>Occupant Name: {{ Auth::guard('occupant')->user()->full_name }}</p>
                <form method="POST" action="{{ route('occupant.auth.logout') }}">
                    @csrf

                    <button type="submit"
                        style="background-color: #dc3545; color: white; padding: 8px 15px; border: none; border-radius: 5px; cursor: pointer;">
                        Logout
                    </button>
                </form>
            </div>

            <!-- Info Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-blue-500 text-white rounded-lg p-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h5 class="text-lg font-medium">Kamar</h5>
                            <h2 class="text-3xl font-bold">A-205</h2>
                        </div>
                        <div>
                            <i class="fas fa-home text-4xl opacity-80"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-green-500 text-white rounded-lg p-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h5 class="text-lg font-medium">Status</h5>
                            <h4 class="text-2xl font-bold">Aktif</h4>
                        </div>
                        <div>
                            <i class="fas fa-check-circle text-4xl opacity-80"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-yellow-500 text-white rounded-lg p-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h5 class="text-lg font-medium">Tagihan</h5>
                            <h4 class="text-2xl font-bold">Rp 350.000</h4>
                        </div>
                        <div>
                            <i class="fas fa-money-bill text-4xl opacity-80"></i>
                        </div>
                    </div>
                </div>
                <div class="bg-cyan-500 text-white rounded-lg p-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h5 class="text-lg font-medium">Kontrak</h5>
                            <h4 class="text-2xl font-bold">8 Bulan</h4>
                        </div>
                        <div>
                            <i class="fas fa-file-contract text-4xl opacity-80"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Profile Info -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="border-b border-gray-200 pb-4 mb-4">
                            <h5 class="text-xl font-semibold">Informasi Penghuni</h5>
                        </div>
                        <div class="text-center mb-6">
                            <img src="https://via.placeholder.com/100x100" class="rounded-full mx-auto" alt="Profile">
                        </div>
                        <div class="space-y-3">
                            <div class="flex">
                                <span class="font-semibold w-24">Nama</span>
                                <span>: Ahmad Rizki</span>
                            </div>
                            <div class="flex">
                                <span class="font-semibold w-24">NIM</span>
                                <span>: 2103118001</span>
                            </div>
                            <div class="flex">
                                <span class="font-semibold w-24">Fakultas</span>
                                <span>: Teknik</span>
                            </div>
                            <div class="flex">
                                <span class="font-semibold w-24">Jurusan</span>
                                <span>: Informatika</span>
                            </div>
                            <div class="flex">
                                <span class="font-semibold w-24">No. HP</span>
                                <span>: 081234567890</span>
                            </div>
                            <div class="flex">
                                <span class="font-semibold w-24">Masa Tinggal</span>
                                <span>: Jan 2024 - Sep 2024</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activities & Payments -->
                <div class="lg:col-span-2">
                    <div class="space-y-6">
                        <!-- Payment History -->
                        <div class="bg-white rounded-lg shadow p-6">
                            <div class="border-b border-gray-200 pb-4 mb-4">
                                <h5 class="text-xl font-semibold">Riwayat Pembayaran</h5>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full">
                                    <thead>
                                        <tr class="border-b">
                                            <th class="text-left py-3">Bulan</th>
                                            <th class="text-left py-3">Jumlah</th>
                                            <th class="text-left py-3">Tanggal Bayar</th>
                                            <th class="text-left py-3">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class="border-b">
                                            <td class="py-3">Februari 2024</td>
                                            <td class="py-3">Rp 350.000</td>
                                            <td class="py-3">01 Feb 2024</td>
                                            <td class="py-3"><span
                                                    class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">Lunas</span>
                                            </td>
                                        </tr>
                                        <tr class="border-b">
                                            <td class="py-3">Januari 2024</td>
                                            <td class="py-3">Rp 350.000</td>
                                            <td class="py-3">02 Jan 2024</td>
                                            <td class="py-3"><span
                                                    class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">Lunas</span>
                                            </td>
                                        </tr>
                                        <tr class="border-b">
                                            <td class="py-3">Maret 2024</td>
                                            <td class="py-3">Rp 350.000</td>
                                            <td class="py-3">-</td>
                                            <td class="py-3"><span
                                                    class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded">Belum
                                                    Bayar</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Announcements -->
                        <div class="bg-white rounded-lg shadow p-6">
                            <div class="border-b border-gray-200 pb-4 mb-4">
                                <h5 class="text-xl font-semibold">Pengumuman</h5>
                            </div>
                            <div class="space-y-4">
                                <div class="bg-blue-50 border border-blue-200 rounded p-4">
                                    <strong class="text-blue-800">Pengumuman!</strong>
                                    <span class="text-blue-700">Pembayaran bulan Maret 2024 jatuh tempo pada tanggal 5
                                        Maret 2024.</span>
                                </div>
                                <div class="bg-yellow-50 border border-yellow-200 rounded p-4">
                                    <strong class="text-yellow-800">Reminder:</strong>
                                    <span class="text-yellow-700">Harap menjaga kebersihan area bersama dan tidak
                                        membuat keributan setelah jam 22:00.</span>
                                </div>
                                <div class="bg-green-50 border border-green-200 rounded p-4">
                                    <strong class="text-green-800">Info:</strong>
                                    <span class="text-green-700">Fasilitas WiFi telah diperbaiki dan dapat digunakan
                                        kembali.</span>
                                </div>
                            </div>
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

<div class="flex flex-col gap-6">
    <x-managers.ui.card class="p-6">
        <h3 class="text-xl font-bold mb-4">Laporan Periodik</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
            <div>
                <label for="filterType" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pilih
                    Waktu</label>
                <select wire:model.live="filterType" id="filterType"
                    class="block w-full rounded-md shadow-sm border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 dark:bg-zinc-800 dark:border-gray-600 dark:text-white">
                    <option value="all_time">Semua Waktu</option>
                    <option value="daily">Harian</option>
                    <option value="monthly">Bulanan</option>
                    <option value="yearly">Tahunan</option>
                    <option value="custom">Custom Tanggal</option>
                </select>
            </div>

            @if ($filterType === 'custom')
                <div>
                    <label for="startDate" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Dari
                        Tanggal</label>
                    <input type="date" wire:model.live="startDate" id="startDate"
                        class="block w-full rounded-md shadow-sm border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 dark:bg-zinc-800 dark:border-gray-600 dark:text-white">
                </div>
                <div>
                    <label for="endDate" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Sampai
                        Tanggal</label>
                    <input type="date" wire:model.live="endDate" id="endDate"
                        class="block w-full rounded-md shadow-sm border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 dark:bg-zinc-800 dark:border-gray-600 dark:text-white">
                </div>
            @endif

            <div>
                <label for="occupantFilter"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pilih Penghuni</label>
                <select wire:model.live="occupantFilter" id="occupantFilter"
                    class="block w-full rounded-md shadow-sm border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 dark:bg-zinc-800 dark:border-gray-600 dark:text-white">
                    <option value="">Semua Penghuni</option>
                    @foreach ($occupantOptions as $option)
                        <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="contractFilter"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pilih Kontrak</label>
                <select wire:model.live="contractFilter" id="contractFilter"
                    class="block w-full rounded-md shadow-sm border-gray-300 focus:border-emerald-500 focus:ring-emerald-500 dark:bg-zinc-800 dark:border-gray-600 dark:text-white">
                    <option value="">Semua Kontrak</option>
                    @foreach ($contractOptions as $option)
                        <option value="{{ $option['value'] }}">{{ $option['label'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </x-managers.ui.card>
    {{-- Image: Report Filter --}}

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <x-managers.ui.card>
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Pendapatan Total</p>
            <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400 mt-1">
                Rp{{ number_format($totalRevenue, 0, ',', '.') }}</p>
        </x-managers.ui.card>

        <x-managers.ui.card>
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Pendapatan Rata-rata Per Hari</p>
            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1">
                Rp{{ number_format($averageDailyRevenue, 0, ',', '.') }}</p>
        </x-managers.ui.card>

        <x-managers.ui.card>
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Pendapatan Rata-rata Per Bulan</p>
            <p class="text-2xl font-bold text-purple-600 dark:text-purple-400 mt-1">
                Rp{{ number_format($averageMonthlyRevenue, 0, ',', '.') }}</p>
        </x-managers.ui.card>

        <x-managers.ui.card>
            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Rata-rata Per Invoice</p>
            <p class="text-2xl font-bold text-orange-600 dark:text-orange-400 mt-1">
                Rp{{ number_format($averagePerInvoice, 0, ',', '.') }}</p>
        </x-managers.ui.card>
    </div>
    {{-- Image: Summary Cards --}}

    <x-managers.ui.card>
        <h3 class="text-xl font-bold mb-4">Grafik Pemasukan</h3>
        <div style="height: 400px;">
            <canvas id="incomeChart"></canvas>
        </div>
    </x-managers.ui.card>
    {{-- Image: Income Chart --}}

    <x-managers.ui.card class="p-0">
        <h3 class="text-xl font-bold p-6 pb-0">Riwayat Tagihan</h3>
        <div class="p-6">
            <div class="flex justify-end items-center gap-2 mb-4">
                <span>Baris</span>
                <div class="w-22">
                    <x-managers.form.input wire:model.live="perPage" type="number" placeholder="10" />
                </div>
                <span>Unduh</span>
                <x-managers.ui.button-export />
            </div>
        </div>
        <x-managers.table.table :headers="[
            'No. Invoice',
            'Tanggal Pembayaran',
            'Deskripsi',
            'Nama Penghuni',
            'Kamar',
            'Jenis',
            'Nominal',
            'Aksi',
        ]">
            <x-managers.table.body>
                @forelse($recentInvoices as $invoice)
                    <x-managers.table.row wire:key="{{ $invoice->id }}">
                        <x-managers.table.cell>{{ $invoice->invoice_number }}</x-managers.table.cell>
                        <x-managers.table.cell>{{ $invoice->paid_at ? $invoice->paid_at->translatedFormat('d M Y') : '-' }}</x-managers.table.cell>
                        <x-managers.table.cell>{{ $invoice->description }}</x-managers.table.cell>
                        <x-managers.table.cell>{{ $invoice->contract->occupants->first()->full_name ?? 'N/A' }}</x-managers.table.cell>
                        <x-managers.table.cell>{{ $invoice->contract->unit->room_number ?? 'N/A' }}</x-managers.table.cell>
                        <x-managers.table.cell>
                            @if ($invoice->contract->pricing_basis)
                                <x-managers.ui.avatar type="{{ $invoice->contract->pricing_basis->label() }}">
                                    {{ $invoice->contract->pricing_basis->label() }}
                                </x-managers.ui.avatar>
                            @else
                                -
                            @endif
                        </x-managers.table.cell>
                        <x-managers.table.cell>Rp{{ number_format($invoice->amount, 0, ',', '.') }}</x-managers.table.cell>
                        <x-managers.table.cell class="text-right">
                            <div class="flex gap-2 justify-end">
                                <x-managers.ui.button wire:click="viewInvoiceDetails({{ $invoice->id }})"
                                    variant="info" size="sm" title="Lihat Detail Invoice">
                                    <flux:icon name="arrow-path" class="w-4" />
                                </x-managers.ui.button>
                            </div>
                        </x-managers.table.cell>
                    </x-managers.table.row>
                @empty
                    <x-managers.table.row>
                        <x-managers.table.cell colspan="8" class="text-center text-gray-500">
                            Tidak ada riwayat tagihan ditemukan.
                        </x-managers.table.cell>
                    </x-managers.table.row>
                @endforelse
            </x-managers.table.body>
        </x-managers.table.table>
        <x-managers.ui.pagination :paginator="$recentInvoices" />
    </x-managers.ui.card>
    {{-- Image: Recent Transactions Table --}}
</div>

@script
    <script>
        let incomeChart;

        const createChart = (labels, data) => {
            const ctx = document.getElementById('incomeChart').getContext('2d');
            if (incomeChart) {
                incomeChart.destroy(); // Destroy previous chart instance
            }
            incomeChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Pendapatan',
                        data: data,
                        backgroundColor: 'rgba(52, 211, 153, 0.2)', // Emerald-400 with opacity
                        borderColor: 'rgba(52, 211, 153, 1)', // Emerald-400
                        borderWidth: 2,
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) {
                                        label += ': ';
                                    }
                                    if (context.parsed.y !== null) {
                                        label += 'Rp' + new Intl.NumberFormat('id-ID').format(context.parsed
                                            .y);
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value, index, ticks) {
                                    return 'Rp' + new Intl.NumberFormat('id-ID').format(value);
                                }
                            }
                        }
                    }
                }
            });
        };

        // Initial chart creation on component load
        createChart(@json($chartLabels), @json($chartData));

        // Listen for updateChart event from Livewire
        Livewire.on('updateChart', (event) => {
            createChart(event.labels, event.data);
        });
    </script>
@endscript

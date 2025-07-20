<div class="flex flex-col gap-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold text-zinc-900 dark:text-zinc-100">Laporan Pendapatan</h1>
            <p class="text-sm text-zinc-500 dark:text-zinc-400">Analisis dan laporan pendapatan Rusunawa UNJ</p>
        </div>

        <!-- Export Actions -->
        <div class="flex items-center gap-3">
            <flux:button wire:click="exportExcel" variant="outline" size="sm" icon="document-arrow-down">
                Export Excel
            </flux:button>
            <flux:button wire:click="exportPdf" variant="outline" size="sm" icon="document-arrow-down">
                Export PDF
            </flux:button>
        </div>
    </div>

    <!-- Filters -->
    @include('livewire.managers.oprations.income-reports.partials._filters')

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Total Pendapatan</p>
                    <p class="text-2xl font-bold text-green-600 dark:text-green-400 mt-1">
                        Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center">
                    <flux:icon.banknotes class="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs text-zinc-500 dark:text-zinc-400">
                <flux:icon.calendar class="w-3 h-3 mr-1" />
                @if ($startDate && $endDate)
                    {{ \Carbon\Carbon::parse($startDate)->translatedFormat('d M Y') }} -
                    {{ \Carbon\Carbon::parse($endDate)->translatedFormat('d M Y') }}
                @else
                    Semua Waktu
                @endif
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Rata-rata Harian</p>
                    <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-1">
                        Rp {{ number_format($averageDailyRevenue, 0, ',', '.') }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                    <flux:icon.calendar-days class="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs text-zinc-500 dark:text-zinc-400">
                <flux:icon.arrow-trending-up class="w-3 h-3 mr-1" />
                Per hari dalam periode
            </div>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400">Rata-rata Bulanan</p>
                    <p class="text-2xl font-bold text-purple-600 dark:text-purple-400 mt-1">
                        Rp {{ number_format($averageMonthlyRevenue, 0, ',', '.') }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/20 rounded-lg flex items-center justify-center">
                    <flux:icon.chart-bar class="w-6 h-6 text-purple-600 dark:text-purple-400" />
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs text-zinc-500 dark:text-zinc-400">
                <flux:icon.arrow-trending-up class="w-3 h-3 mr-1" />
                Proyeksi bulanan
            </div>
        </div>
    </div>

    <!-- Chart Section -->
    <div
        class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
            <h3 class="text-lg font-semibold text-zinc-900 dark:text-zinc-100">Trend Pendapatan</h3>
            <p class="text-sm text-zinc-500 dark:text-zinc-400 mt-1">Grafik perkembangan pendapatan dalam periode yang
                dipilih</p>
        </div>

        <div class="px-6 py-6" wire:key="chart-section-{{ $chartKey }}"
            data-chart-data="{{ json_encode($chartData) }}">
            @if ($chartData && isset($chartData['labels']) && count($chartData['labels']) > 0)
                <div class="h-64" wire:key="chart-container-{{ $chartKey }}">
                    <canvas id="incomeChart" width="400" height="200"></canvas>
                </div>
            @else
                <div class="h-64 flex items-center justify-center" wire:key="no-data-{{ $chartKey }}">
                    <div class="text-center">
                        <div class="w-16 h-16 mx-auto mb-4 text-zinc-300 dark:text-zinc-600">
                            <flux:icon.chart-bar class="w-full h-full" />
                        </div>
                        <h3 class="text-lg font-medium text-zinc-900 dark:text-zinc-100 mb-2">Tidak ada data</h3>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">Tidak ada data pendapatan untuk periode yang
                            dipilih</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Table Section -->
    @include('livewire.managers.oprations.income-reports.partials._table')

    <!-- Invoice Detail Modal -->
    @include('livewire.managers.oprations.income-reports.partials._modal-detail')
</div>

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let chart = null;

            const initChart = (data) => {
                const ctx = document.getElementById('incomeChart');
                if (!ctx) {
                    return;
                }

                if (chart) {
                    chart.destroy();
                    chart = null;
                }

                if (!data || !data.labels || !data.datasets || data.labels.length === 0) {
                    return;
                }

                chart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels,
                        datasets: [{
                            label: 'Pendapatan (Rp)',
                            data: data.datasets[0].data,
                            borderColor: 'rgb(34, 197, 94)',
                            backgroundColor: 'rgba(34, 197, 94, 0.1)',
                            tension: 0.4,
                            fill: true,
                            pointBackgroundColor: 'rgb(34, 197, 94)',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
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
                                        return 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                                    }
                                }
                            }
                        },
                        scales: {
                            x: {
                                grid: {
                                    display: false
                                }
                            },
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.05)'
                                },
                                ticks: {
                                    callback: function(value) {
                                        return 'Rp ' + value.toLocaleString('id-ID', {
                                            notation: 'compact'
                                        });
                                    }
                                }
                            }
                        }
                    }
                });
            }

            // Initialize chart
            const initialData = @json($chartData);
            if (initialData && initialData.labels && initialData.labels.length > 0) {
                initChart(initialData);
            }

            // Listen for chart updates
            Livewire.on('updateChart', (event) => {
                const chartData = Array.isArray(event) ? event[0] : event;

                // Wait a bit for DOM to update
                setTimeout(() => {
                    if (chartData && chartData.labels && chartData.labels.length > 0) {
                        initChart(chartData);
                    } else {
                        if (chart) {
                            chart.destroy();
                            chart = null;
                        }
                    }
                }, 100);
            });

            // Also listen for Livewire component updates
            document.addEventListener('livewire:updated', function() {
                setTimeout(() => {
                    const canvas = document.getElementById('incomeChart');

                    if (canvas) {
                        // Get updated chart data from the canvas data attribute if available
                        const chartDataElement = document.querySelector('[data-chart-data]');
                        let currentData = null;

                        if (chartDataElement) {
                            try {
                                currentData = JSON.parse(chartDataElement.getAttribute(
                                    'data-chart-data'));
                            } catch (e) {
                                // Silent fail
                            }
                        }

                        if (currentData && currentData.labels && currentData.labels.length > 0) {
                            initChart(currentData);
                        } else if (chart) {
                            chart.destroy();
                            chart = null;
                        }
                    } else if (chart) {
                        chart.destroy();
                        chart = null;
                    }
                }, 150);
            });
        });
    </script>
@endpush
